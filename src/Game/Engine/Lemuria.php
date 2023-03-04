<?php
declare(strict_types = 1);
namespace App\Game\Engine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use Lemuria\Engine\Fantasya\Factory\Model\LemuriaNewcomer;
use Lemuria\Engine\Fantasya\Storage\LemuriaConfig;
use Lemuria\Engine\Fantasya\Storage\NewcomerConfig;
use Lemuria\Engine\Newcomer;
use Lemuria\Exception\UnknownUuidException;
use Lemuria\Id;
use Lemuria\Lemuria as LemuriaGame;
use Lemuria\Model\Domain;
use Lemuria\Model\Exception\NotRegisteredException;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Party as PartyModel;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Version\VersionFinder;

use App\Data\Newbie as NewbieData;
use App\Entity\Assignment;
use App\Entity\Game;
use App\Entity\User;
use App\Game\Engine;
use App\Game\Newbie;
use App\Game\Party;
use App\Game\Race;
use App\Game\Statistics;
use App\Repository\AssignmentRepository;

class Lemuria implements Engine
{
	use BuilderTrait;

	private const LOG_FILE = 'website-engine.log';

	private static bool $hasBeenInitialized = false;

	private static bool $hasBeenChanged = false;

	private static NewcomerConfig $config;

	private static array $retiredUuids = [];

	private EntityManagerInterface $entityManager;

	public function __construct(private readonly ContainerBagInterface $container, private readonly AssignmentRepository $assignmentRepository,
								ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
		if (!self::$hasBeenInitialized) {
			self::$config = new NewcomerConfig(__DIR__ . '/../../../var/lemuria');
			LemuriaGame::init(self::$config->setLogFile(self::LOG_FILE));
			LemuriaGame::load();
			foreach ($this->assignmentRepository->findRetired() as $assignment) {
				self::$retiredUuids[$assignment->getUuid()] = true;
			}
			self::$hasBeenInitialized = true;
		}
	}

	function __destruct() {
		if (self::$hasBeenChanged) {
			LemuriaGame::save();
			self::$hasBeenChanged = false;
		}
	}

	public function canSimulate(Game $game, int $turn): bool {
		return $turn === $this->getRound($game);
	}

	public function getRulesFile(): string {
		return __DIR__ . '/../../../var/check/lemuria.tpl';
	}

	public function getRound(Game $game): int {
		return LemuriaGame::Calendar()->Round();
	}

	public function getLastZat(Game $game): \DateTime {
		$dateTime = new \DateTime();
		return $dateTime->setTimestamp(self::$config[LemuriaConfig::MDD]);
	}

	/**
	 * @return array<Party>
	 */
	public function getAll(Game $game): array {
		$parties = [];
		foreach (LemuriaGame::Catalog()->getAll(Domain::Party) as $party) {
			/** @var PartyModel $party */
			if ($party->hasRetired()) {
				$this->updateRetirement($party);
			} else {
				$parties[] = $this->createParty($party);
			}
		}
		return $parties;
	}

	public function getById(string $id, Game $game): ?Party {
		try {
			/** @var PartyModel $party */
			$party = LemuriaGame::Catalog()->get(Id::fromId($id), Domain::Party);
			return $this->createParty($party);
		} catch (NotRegisteredException) {
			return null;
		}
	}

	public function getByOwner(string $owner, Game $game): ?Party {
		/** @var PartyModel $party */
		$party = LemuriaGame::Registry()->find($owner);
		return $party ? $this->createParty($party) : null;
	}

	/**
	 * @return array<Party>
	 */
	public function getParties(User $user, Game $game): array {
		$parties = [];
		foreach ($this->assignmentRepository->findByUser($user) as $assignment) {
			/** @var PartyModel $party */
			$party = LemuriaGame::Registry()->find($assignment->getUuid());
			if ($party) {
				if ($party->hasRetired()) {
					$this->updateRetirement($party);
				} else {
					$parties[] = $this->createParty($party);
				}
			}
		}
		return $parties;
	}

	/**
	 * @return array<Newbie>
	 */
	public function getNewbies(User $user, Game $game): array {
		$newbies = [];
		foreach ($this->assignmentRepository->findByUser($user) as $assignment) {
			try{
				$newcomer  = LemuriaGame::Debut()->get($assignment->getUuid());
				$newbies[] = $this->createNewbie($newcomer, $user);
			} catch (UnknownUuidException) {
			}
		}
		return $newbies;
	}

	public function getStatistics(Game $game): Statistics {
		return new LemuriaStatistics();
	}

	public function getVersion(): string {
		$versionFinder = new VersionFinder($this->container->get('app.game.lemuria'));
		$version = $versionFinder->get();
		return $version->name . ' ' . $version->version;
	}

	public function updateUser(User $user, Game $game): void {
	}

	public function create(Newbie $newbie, Game $game): void {
		$name        = $newbie->getName();
		$description = $newbie->getDescription();
		$race        = new Race($newbie->getRace());
		$race        = self::createRace($race->toLemuria());
		$newcomer    = new LemuriaNewcomer();
		$newcomer->setName($name)->setDescription($description)->setRace($race);
		LemuriaGame::Debut()->add($newcomer);
		self::$hasBeenChanged = true;

		$assignment = new Assignment();
		$assignment->setUser($newbie->getUser());
		$assignment->setUuid($newcomer->Uuid());
		$this->entityManager->persist($assignment);
		$this->entityManager->flush();
	}

	public function delete(Newbie $newbie, Game $game): void {
		try {
			$newcomer = LemuriaGame::Debut()->get($newbie->getUuid());
			LemuriaGame::Debut()->remove($newcomer);
			self::$hasBeenChanged = true;
		} catch (UnknownUuidException) {
		}
		$assignment = $this->assignmentRepository->findByUuid($newbie->getUuid());
		$this->entityManager->remove($assignment);
		$this->entityManager->flush();
	}

	private function createParty(PartyModel $party): Party {
		$uuid  = $party->Uuid();
		$email = $this->fetchEmailAddress($uuid);
		$user  = $this->assignmentRepository->findByUuid($party->Uuid())?->getUser()->getId();
		return new Party([
			'id'           => (string)$party->Id(),
			'rasse'        => (string)Race::lemuria((string)$party->Race()),
			'name'         => $party->Name(),
			'beschreibung' => $party->Description(),
			'owner_id'     => $uuid,
			'user_id'      => $user,
			'email'        => $email,
			'monster'      => $party->Type() !== Type::Player,
			'retirement'   => $party->Retirement()
		]);
	}

	private function createNewbie(Newcomer $newcomer, User $user): Newbie {
		if ($newcomer instanceof LemuriaNewcomer) {
			$data = new NewbieData();
			$data->setName($newcomer->Name());
			$data->setDescription($newcomer->Description());
			$data->setRace((string)Race::lemuria((string)$newcomer->Race()));
			return Newbie::fromData($data)->setUser($user)->setUuid($newcomer->Uuid());
		}
		throw new \RuntimeException('Invalid Newcomer object.');
	}

	private function fetchEmailAddress(string $uuid): string {
		$assignment = $this->assignmentRepository->findByUuid($uuid);
		return $assignment ? $assignment->getUser()->getEmail() : '';
	}

	private function updateRetirement(PartyModel $party): void {
		if (!isset(self::$retiredUuids[$party->Uuid()])) {
			$assignment = $this->assignmentRepository->findByUuid($party->Uuid());
			$assignment->retire();
			$this->entityManager->persist($assignment);
			$this->entityManager->flush();
		}
	}
}

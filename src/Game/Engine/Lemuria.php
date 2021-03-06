<?php
declare(strict_types = 1);
namespace App\Game\Engine;

use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Lemuria\Engine\Lemuria\Storage\LemuriaConfig;
use Lemuria\Id;
use Lemuria\Lemuria as LemuriaGame;
use Lemuria\Model\Catalog;
use Lemuria\Model\Exception\NotRegisteredException;
use Lemuria\Model\Lemuria\Party as PartyModel;

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
	private static bool $hasBeenInitialized = false;

	public function __construct(private AssignmentRepository $assignmentRepository, private EntityManagerInterface $entityManager) {
		if (!self::$hasBeenInitialized) {
			LemuriaGame::init(new LemuriaConfig(__DIR__ . '/../../../var/lemuria'));
			LemuriaGame::load();
			self::$hasBeenInitialized = true;
		}
	}

	public function getRound(Game $game): int {
		return LemuriaGame::Calendar()->Round() - 1;
	}

	public function getLastZat(Game $game): \DateTime {
		$dateTime = new \DateTime();
		return $dateTime->sub(new \DateInterval('P1D'))->setTime(6, 0);
	}

	public function getById(string $id, Game $game): ?Party {
		try {
			/** @var PartyModel $party */
			$party = LemuriaGame::Catalog()->get(Id::fromId($id), Catalog::PARTIES);
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
	 * @return Party[]
	 */
	public function getParties(User $user, Game $game): array {
		$parties = [];
		foreach ($this->assignmentRepository->findFor($user) as $assignment) {
			/** @var PartyModel $party */
			$party     = LemuriaGame::Registry()->find($assignment->getUuid());
			$parties[] = $this->createParty($party);
		}
		return $parties;
	}

	/**
	 * @return Newbie[]
	 */
	public function getNewbies(User $user, Game $game): array {
		$newbies = [];
		foreach ($this->assignmentRepository->findNewbiesFor($user) as $assignment) {
			$newbies[] = Newbie::fromAssignment($assignment);
		}
		return $newbies;
	}

	#[Pure] public function getStatistics(Game $game): Statistics {
		return new LemuriaStatistics($game, $this->assignmentRepository);
	}

	public function updateUser(User $user, Game $game): void {
	}

	public function create(Newbie $newbie, Game $game): void {
		$assignment = new Assignment();
		$assignment->setUser($newbie->getUser());
		$assignment->setUuid($newbie->getUuid());
		$assignment->setNewbie($newbie->toLemuriaJson());
		$this->entityManager->persist($assignment);
		$this->entityManager->flush();
	}

	public function delete(Newbie $newbie, Game $game): void {
		foreach ($this->assignmentRepository->findNewbiesFor($newbie->getUser()) as $assignment) {
			if ($assignment->getUuid() === $newbie->getUuid()) {
				$this->entityManager->remove($assignment);
			}
		}
		$this->entityManager->flush();
	}

	private function createParty(PartyModel $party): Party {
		return new Party([
			'id'           => (string)$party->Id(),
			'rasse'        => (string)Race::lemuria((string)$party->Race()),
			'name'         => $party->Name(),
			'beschreibung' => $party->Description(),
			'owner_id'     => (string)$party->Uuid(),
			'user_id'      => $party->Uuid(),
			'email'        => ''
		]);
	}
}

<?php
declare (strict_types = 1);
namespace App\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Game;
use App\Entity\User;
use App\Game\Newbie;
use App\Game\Party;

/**
 * A service for fetching parties.
 */
class PartyService
{
	/**
	 * @var GameService
	 */
	private $service;

	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * @param GameService $service
	 * @param EntityManagerInterface $manager
	 */
	public function __construct(GameService $service, EntityManagerInterface $manager) {
		$this->service = $service;
		$this->manager = $manager;
	}

	/**
	 * Get all parties of a User.
	 *
	 * @param User $user
	 * @return array
	 * @throws DBALException
	 */
	public function getFor(User $user): array {
		$games   = $this->service->getAll();
		$parties = [];
		foreach ($games as $game) {
			$parties[$game->getId()] = $this->parties($user, $game);
		}
		return $parties;
	}

	/**
	 * Get parties in current Game of a User.
	 *
	 * @param User $user
	 * @return Party[]
	 * @throws DBALException
	 */
	public function getCurrent(User $user): array {
		return $this->parties($user, $this->service->getCurrent());
	}

	/**
	 * Get all newbies of a User.
	 *
	 * @param User $user
	 * @return Newbie[]
	 * @throws DBALException
	 */
	public function getNewbies(User $user): array {
		$games   = $this->service->getAll();
		$newbies = [];
		foreach ($games as $game) {
			$newbies[$game->getId()] = $this->newbies($user, $game);
		}
		return $newbies;
	}

	/**
	 * Check if a User has a Party in a Game.
	 *
	 * @param User $user
	 * @param Game $game
	 * @return bool
	 * @throws DBALException
	 */
	public function hasParty(User $user, Game $game): bool {
		$parties = $this->getFor($user);
		return !empty($parties[$game->getId()]);
	}

	/**
	 * Check if a User has a Newbie in a Game.
	 *
	 * @param User $user
	 * @param Game $game
	 * @return bool
	 * @throws DBALException
	 */
	public function hasNewbie(User $user, Game $game): bool {
		$newbies = $this->getNewbies($user);
		return !empty($newbies[$game->getId()]);
	}

	/**
	 * Check if a User has a Party or Newbie in a Game.
	 *
	 * @param User $user
	 * @param Game $game
	 * @return bool
	 * @throws DBALException
	 */
	public function hasAny(User $user, Game $game): bool {
		return $this->hasParty($user, $game) || $this->hasNewbie($user, $game);
	}

	/**
	 * Create a Newbie.
	 *
	 * @param Newbie $newbie
	 * @throws DBALException
	 */
	public function create(Newbie $newbie) {
		$connection = $this->manager->getConnection();
		$table      = $this->service->getCurrent()->getDb() . '.neuespieler';
		$columns    = implode(',', array_keys($newbie->getProperties()));
		$values     = $this->createValues($newbie);
		$sql        = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
		if (!$connection->prepare($sql)->execute()) {
			throw new DBALException('Could not save Newbie.');
		}
	}

	/**
	 * Delete a Newbie.
	 *
	 * @param Newbie $newbie
	 */
	public function delete(Newbie $newbie) {
		$connection = $this->manager->getConnection();
		$table      = $this->service->getCurrent()->getDb() . '.neuespieler';
		$values     = $this->createConstraints($newbie);
		$sql        = "DELETE FROM " . $table . " WHERE " . $values;
		if (!$connection->prepare($sql)->execute()) {
			throw new DBALException('Could not delete Newbie.');
		}
	}

	/**
	 * @param User $user
	 * @param Game $game
	 * @return Party[]
	 * @throws DBALException
	 */
	private function parties(User $user, Game $game): array {
		$connection = $this->manager->getConnection();
		$table      = $game->getDb() . '.partei';
		$sql        = "SELECT * FROM " . $table . " WHERE user_id = " . $user->getId();
		$stmt       = $connection->prepare($sql);
		$stmt->execute();
		$parties = [];
		foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $properties) {
			$parties[] = new Party($properties);
		}
		return $parties;
	}

	/**
	 * @param User $user
	 * @param Game $game
	 * @return Newbie[]
	 * @throws DBALException
	 */
	private function newbies(User $user, Game $game): array {
		$connection = $this->manager->getConnection();
		$table      = $game->getDb() . '.neuespieler';
		$sql        = "SELECT * FROM " . $table . " WHERE user_id = " . $user->getId();
		$stmt       = $connection->prepare($sql);
		$stmt->execute();
		$newbies = [];
		foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $properties) {
			$newbie    = new Newbie($properties);
			$newbies[] = $newbie->setUser($user);
		}
		return $newbies;
	}

	/**
	 * @var Newbie $newbie
	 * @return string
	 */
	private function createValues(Newbie $newbie): string {
		$connection = $this->manager->getConnection();
		$properties = [];
		foreach ($newbie->getProperties() as $value) {
			$properties[] = is_int($value) ? $value : $connection->quote($value);
		}
		return implode(',', $properties);
	}

	/**
	 * @var Newbie $newbie
	 * @return string
	 */
	private function createConstraints(Newbie $newbie): string {
		$connection  = $this->manager->getConnection();
		$constraints = [];
		foreach ($newbie->getProperties() as $column => $value) {
			$constraints[] = $column . ' = ' . (is_int($value) ? $value : $connection->quote($value));
		}
		return implode(' AND ', $constraints);
	}
}

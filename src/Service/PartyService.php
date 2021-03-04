<?php
declare (strict_types = 1);
namespace App\Service;

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
	public function __construct(private GameService $service, private EntityManagerInterface $manager) {
	}

	public function getById(string $id, Game $game): ?Party {
		$connection = $this->manager->getConnection();
		$table      = $game->getDb() . '.partei';
		$sql        = "SELECT * FROM " . $table . " WHERE id = " . $this->manager->getConnection()->quote($id);
		$stmt       = $connection->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAllAssociative();
		if (is_array($result) && isset($result[0]) && is_array($result[0])) {
			return new Party($result[0]);
		}
		return null;
	}

	/**
	 * Get all parties of a User.
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
	 * @return Party[]
	 */
	public function getCurrent(User $user): array {
		return $this->parties($user, $this->service->getCurrent());
	}

	/**
	 * Get all newbies of a User.
	 *
	 * @return Newbie[]
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
	 */
	public function hasParty(User $user, Game $game): bool {
		$parties = $this->getFor($user);
		return !empty($parties[$game->getId()]);
	}

	/**
	 * Check if a User has a Newbie in a Game.
	 */
	public function hasNewbie(User $user, Game $game): bool {
		$newbies = $this->getNewbies($user);
		return !empty($newbies[$game->getId()]);
	}

	/**
	 * Check if a User has a Party or Newbie in a Game.
	 */
	public function hasAny(User $user, Game $game): bool {
		return $this->hasParty($user, $game) || $this->hasNewbie($user, $game);
	}

	/**
	 * Update eMail address of user's parties and newbies.
	 */
	public function update(User $user) {
		$games      = $this->service->getAll();
		$connection = $this->manager->getConnection();
		$id         = $user->getId();
		$email      = $connection->quote($user->getEmail());

		foreach ($games as $game) {
			$table = $game->getDb() . '.partei';
			$sql   = "UPDATE " . $table . " SET email = " . $email . " WHERE user_id = " . $id;
			if (!$connection->prepare($sql)->execute()) {
				throw new \RuntimeException('Could not update parties.');
			}

			$table = $game->getDb() . '.neuespieler';
			$sql   = "UPDATE " . $table . " SET email = " . $email . " WHERE user_id = " . $id;
			if (!$connection->prepare($sql)->execute()) {
				throw new \RuntimeException('Could not update newbies.');
			}
		}
	}

	public function create(Newbie $newbie) {
		$connection = $this->manager->getConnection();
		$table      = $this->service->getCurrent()->getDb() . '.neuespieler';
		$columns    = implode(',', array_keys($newbie->getProperties()));
		$values     = $this->createValues($newbie);
		$sql        = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
		if (!$connection->prepare($sql)->execute()) {
			throw new \RuntimeException('Could not save Newbie.');
		}
	}

	public function delete(Newbie $newbie) {
		$connection = $this->manager->getConnection();
		$table      = $this->service->getCurrent()->getDb() . '.neuespieler';
		$values     = $this->createConstraints($newbie);
		$sql        = "DELETE FROM " . $table . " WHERE " . $values;
		if (!$connection->prepare($sql)->execute()) {
			throw new \RuntimeException('Could not delete Newbie.');
		}
	}

	/**
	 * @return Party[]
	 */
	private function parties(User $user, Game $game): array {
		$connection = $this->manager->getConnection();
		$table      = $game->getDb() . '.partei';
		$sql        = "SELECT * FROM " . $table . " WHERE user_id = " . $user->getId();
		$stmt       = $connection->prepare($sql);
		$stmt->execute();
		$parties = [];
		foreach ($stmt->fetchAllAssociative() as $properties) {
			$parties[] = new Party($properties);
		}
		return $parties;
	}

	/**
	 * @return Newbie[]
	 */
	private function newbies(User $user, Game $game): array {
		$connection = $this->manager->getConnection();
		$table      = $game->getDb() . '.neuespieler';
		$sql        = "SELECT * FROM " . $table . " WHERE user_id = " . $user->getId();
		$stmt       = $connection->prepare($sql);
		$stmt->execute();
		$newbies = [];
		foreach ($stmt->fetchAllAssociative() as $properties) {
			$newbie    = new Newbie($properties);
			$newbies[] = $newbie->setUser($user);
		}
		return $newbies;
	}

	private function createValues(Newbie $newbie): string {
		$connection = $this->manager->getConnection();
		$properties = [];
		foreach ($newbie->getProperties() as $value) {
			$properties[] = is_int($value) ? $value : $connection->quote($value);
		}
		return implode(',', $properties);
	}

	private function createConstraints(Newbie $newbie): string {
		$connection  = $this->manager->getConnection();
		$constraints = [];
		foreach ($newbie->getProperties() as $column => $value) {
			$constraints[] = $column . ' = ' . (is_int($value) ? $value : $connection->quote($value));
		}
		return implode(' AND ', $constraints);
	}
}

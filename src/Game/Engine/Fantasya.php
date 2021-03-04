<?php
declare(strict_types = 1);
namespace App\Game\Engine;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Game;
use App\Entity\User;
use App\Game\Engine;
use App\Game\Newbie;
use App\Game\Party;

class Fantasya implements Engine
{
	public function __construct(private EntityManagerInterface $manager) {
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

	public function getRound(Game $game): int {
		$table = $game->getDb() . '.settings';
		$sql   = "SELECT value FROM " . $table . " WHERE name = 'game.runde'";
		$stmt  = $this->manager->getConnection()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchFirstColumn();
		return (int)($result[0] ?? 0);
	}

	public function getLastZat(Game $game): \DateTime {
		$table = $game->getDb() . '.meldungen';
		$sql   = "SELECT MAX(zeit) FROM " . $table;
		$stmt  = $this->manager->getConnection()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchFirstColumn();
		return new \DateTime($result[0] ?? 'now');
	}

	/**
	 * @return Party[]
	 */
	public function getParties(User $user, Game $game): array {
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
	public function getNewbies(User $user, Game $game): array {
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

	public function updateUser(User $user, Game $game): void {
		$id         = $user->getId();
		$connection = $this->manager->getConnection();
		$email      = $connection->quote($user->getEmail());

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

	public function create(Newbie $newbie, Game $game): void {
		$connection = $this->manager->getConnection();
		$table      = $game->getDb() . '.neuespieler';
		$columns    = implode(',', array_keys($newbie->getProperties()));
		$values     = $this->createValues($newbie);
		$sql        = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
		if (!$connection->prepare($sql)->execute()) {
			throw new \RuntimeException('Could not save Newbie.');
		}
	}

	public function delete(Newbie $newbie, Game $game): void {
		$connection = $this->manager->getConnection();
		$table      = $game->getDb() . '.neuespieler';
		$values     = $this->createConstraints($newbie);
		$sql        = "DELETE FROM " . $table . " WHERE " . $values;
		if (!$connection->prepare($sql)->execute()) {
			throw new \RuntimeException('Could not delete Newbie.');
		}
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

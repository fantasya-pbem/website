<?php
declare(strict_types = 1);
namespace App\Game\Engine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Entity\Game;
use App\Entity\User;
use App\Game\Engine;
use App\Game\Newbie;
use App\Game\Party;
use App\Game\Statistics;

class Fantasya implements Engine
{
	private EntityManagerInterface $entityManager;

	public function __construct(private readonly ContainerBagInterface $container, ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
	}

	public function canSimulate(Game $game, int $turn): bool {
		return false;
	}

	public function getRulesFile(): string {
		return __DIR__ . '/../../../var/check/fantasya.tpl';
	}

	/**
	 * @return array<Party>
	 */
	public function getAll(Game $game): array {
		$parties    = [];
		$connection = $this->entityManager->getConnection();
		$table      = $game->getDb() . '.partei';
		$sql        = "SELECT * FROM " . $table;
		$stmt       = $connection->prepare($sql);
		foreach ($stmt->executeQuery()->fetchAllAssociative() as $row) {
			$parties[] = new Party($row);
		}
		return $parties;
	}

	public function getById(string $id, Game $game): ?Party {
		$connection = $this->entityManager->getConnection();
		$table      = $game->getDb() . '.partei';
		$sql        = "SELECT * FROM " . $table . " WHERE id = " . $this->entityManager->getConnection()->quote($id);
		$stmt       = $connection->prepare($sql);
		$result     = $stmt->executeQuery()->fetchAllAssociative();
		if (isset($result[0]) && is_array($result[0])) {
			return new Party($result[0]);
		}
		return null;
	}

	public function getByOwner(string $owner, Game $game): ?Party {
		$connection = $this->entityManager->getConnection();
		$table      = $game->getDb() . '.partei';
		$sql        = "SELECT * FROM " . $table . " WHERE owner_id = " . $this->entityManager->getConnection()->quote($owner);
		$stmt       = $connection->prepare($sql);
		$result     = $stmt->executeQuery()->fetchAllAssociative();
		if (isset($result[0]) && is_array($result[0])) {
			return new Party($result[0]);
		}
		return null;
	}

	public function getRound(Game $game): int {
		$table  = $game->getDb() . '.settings';
		$sql    = "SELECT value FROM " . $table . " WHERE name = 'game.runde'";
		$stmt   = $this->entityManager->getConnection()->prepare($sql);
		$result = $stmt->executeQuery()->fetchFirstColumn();
		return (int)($result[0] ?? 0);
	}

	public function getLastZat(Game $game): \DateTime {
		$table = $game->getDb() . '.meldungen';
		$sql   = "SELECT MAX(zeit) FROM " . $table;
		$stmt  = $this->entityManager->getConnection()->prepare($sql);
		$result = $stmt->executeQuery()->fetchFirstColumn();
		return new \DateTime($result[0] ?? 'now');
	}

	/**
	 * @return array<Party>
	 */
	public function getParties(User $user, Game $game): array {
		$connection = $this->entityManager->getConnection();
		$table      = $game->getDb() . '.partei';
		$sql        = "SELECT * FROM " . $table . " WHERE user_id = " . $user->getId();
		$stmt       = $connection->prepare($sql);
		$parties    = [];
		foreach ($stmt->executeQuery()->fetchAllAssociative() as $properties) {
			$parties[] = new Party($properties);
		}
		return $parties;
	}

	/**
	 * @return array<Newbie>
	 */
	public function getNewbies(User $user, Game $game): array {
		$connection = $this->entityManager->getConnection();
		$table      = $game->getDb() . '.neuespieler';
		$sql        = "SELECT * FROM " . $table . " WHERE user_id = " . $user->getId();
		$stmt       = $connection->prepare($sql);
		$newbies    = [];
		foreach ($stmt->executeQuery()->fetchAllAssociative() as $properties) {
			$newbie    = new Newbie($properties);
			$newbies[] = $newbie->setUser($user);
		}
		return $newbies;
	}

	public function getStatistics(Game $game): Statistics {
		return new FantasyaStatistics($game, $this->entityManager->getConnection());
	}

	public function getVersion(): string {
		$command = $this->container->get('app.game.fantasya');
		exec($command, $output, $result);
		if ($result) {
			return '';
		}
		return $output[0];
	}

	public function updateUser(User $user, Game $game): void {
		if (!$this->hasParty($user, $game)) {
			return;
		}

		$id         = $user->getId();
		$connection = $this->entityManager->getConnection();
		$email      = $connection->quote($user->getEmail());

		$table = $game->getDb() . '.partei';
		$sql   = "UPDATE " . $table . " SET email = " . $email . " WHERE user_id = " . $id;
		if (!$connection->prepare($sql)->executeStatement()) {
			throw new \RuntimeException('Could not update parties.');
		}

		$table = $game->getDb() . '.neuespieler';
		$sql   = "UPDATE " . $table . " SET email = " . $email . " WHERE user_id = " . $id;
		if (!$connection->prepare($sql)->executeStatement()) {
			throw new \RuntimeException('Could not update newbies.');
		}
	}

	public function create(Newbie $newbie, Game $game): void {
		$connection = $this->entityManager->getConnection();
		$table      = $game->getDb() . '.neuespieler';
		$columns    = implode(',', array_keys($newbie->getProperties()));
		$values     = $this->createValues($newbie);
		$sql        = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
		if (!$connection->prepare($sql)->executeStatement()) {
			throw new \RuntimeException('Could not save Newbie.');
		}
	}

	public function delete(Newbie $newbie, Game $game): void {
		$connection = $this->entityManager->getConnection();
		$table      = $game->getDb() . '.neuespieler';
		$values     = $this->createConstraints($newbie);
		$sql        = "DELETE FROM " . $table . " WHERE " . $values;
		if (!$connection->prepare($sql)->executeStatement()) {
			throw new \RuntimeException('Could not delete Newbie.');
		}
	}

	private function hasParty(User $user, Game $game): bool {
		$parties = $this->getParties($user, $game);
		return count($parties) > 0;
	}

	private function createValues(Newbie $newbie): string {
		$connection = $this->entityManager->getConnection();
		$properties = [];
		foreach ($newbie->getProperties() as $value) {
			$properties[] = is_int($value) ? $value : $connection->quote($value);
		}
		return implode(',', $properties);
	}

	private function createConstraints(Newbie $newbie): string {
		$connection  = $this->entityManager->getConnection();
		$constraints = [];
		foreach ($newbie->getProperties() as $column => $value) {
			$constraints[] = $column . ' = ' . (is_int($value) ? $value : $connection->quote($value));
		}
		return implode(' AND ', $constraints);
	}
}

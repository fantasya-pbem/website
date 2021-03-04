<?php
declare (strict_types = 1);
namespace App\Game;

use Doctrine\DBAL\Connection;
use JetBrains\PhpStorm\ArrayShape;

use App\Entity\Game;

class Statistics
{
	/**
	 * @var array[]
	 */
	private array $parties;

	/**
	 * @var array[]
	 */
	private array $newParties;

	public function __construct(private Game $game, private Connection $connection) {
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getParties(): array {
		if ($this->parties === null) {
			$table = $this->game->getDb() . '.partei';
			$sql   = "SELECT name, beschreibung AS description FROM " . $table . " WHERE id NOT IN ('0', 'dark', 'tier') ORDER BY name";
			$stmt  = $this->connection->prepare($sql);
			$stmt->execute();
			$this->parties = $stmt->fetchAllAssociative();
		}
		return $this->parties;
	}

	/**
	 * @throws \Exception
	 */
	public function getPartiesCount(): int {
		return count($this->getParties());
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getPartyRaces(): array {
		$table = $this->game->getDb() . '.partei';
		$sql   = "SELECT rasse AS race, COUNT(*) AS count FROM " . $table . " WHERE id NOT IN ('0', 'dark', 'tier') GROUP BY rasse ORDER BY rasse";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAllAssociative();
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getNewbies(): array {
		if ($this->newParties === null) {
			$table = $this->game->getDb() . '.neuespieler';
			$sql   = "SELECT name, description FROM " . $table . " ORDER BY name";
			$stmt  = $this->connection->prepare($sql);
			$stmt->execute();
			$this->newParties = $stmt->fetchAllAssociative();
		}
		return $this->newParties;
	}

	/**
	 * @throws \Exception
	 */
	public function getNewbiesCount(): int {
		return count($this->getNewbies());
	}

	/**
	 * @throws \Exception
	 */
	#[ArrayShape(['world' => "array|mixed", 'underworld' => "array|mixed"])]
	public function getLandscape(): array {
		$table = $this->game->getDb() . '.regionen';
		$sql   = "SELECT COUNT(*) FROM " . $table . " GROUP BY welt ORDER BY welt DESC";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAllAssociative();
		return ['world' => $result[0], 'underworld' => $result[1]];
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getWorld(): array {
		$table = $this->game->getDb() . '.regionen';
		$sql   = "SELECT typ, COUNT(*) AS count FROM " . $table . " WHERE welt = 1 GROUP BY typ ORDER BY typ";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAllAssociative();
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getUnderworld(): array {
		$table = $this->game->getDb() . '.regionen';
		$sql   = "SELECT typ, COUNT(*) AS count FROM " . $table . " WHERE welt = -1 GROUP BY typ ORDER BY typ";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAllAssociative();
	}

	/**
	 * @throws \Exception
	 */
	public function getPopulation(): array {
		$table = $this->game->getDb() . '.einheiten';
		$sql   = "SELECT COUNT(*) AS units, SUM(person) AS persons FROM " . $table . " GROUP BY welt ORDER BY welt DESC";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAllAssociative();
		return $result[0];
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getRaces(): array {
		$table = $this->game->getDb() . '.einheiten';
		$sql   = "SELECT rasse AS race, COUNT(*) AS units, SUM(person) AS persons FROM " . $table . " WHERE partei NOT IN (620480, 1376883) GROUP BY rasse ORDER BY rasse";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAllAssociative();
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getMonsters(): array {
		$table = $this->game->getDb() . '.einheiten';
		$sql   = "SELECT rasse AS race, COUNT(*) AS units, SUM(person) AS persons FROM " . $table . " WHERE partei IN (620480, 1376883) GROUP BY rasse ORDER BY rasse";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAllAssociative();
	}
}

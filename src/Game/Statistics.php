<?php
declare (strict_types = 1);
namespace App\Game;

use App\Entity\Game;
use Doctrine\DBAL\Connection;

/**
 * A helper class for game statistical data.
 */
class Statistics
{
	/**
	 * @var Game
	 */
	private $game;

	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @var array[]
	 */
	private $parties;

	/**
	 * @var array[]
	 */
	private $newParties;

	/**
	 * @param Game $game
	 * @param Connection $connection
	 */
	public function __construct(Game $game, Connection $connection) {
		$this->game       = $game;
		$this->connection = $connection;
	}

	/**
	 * @return array[]
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getParties(): array {
		if ($this->parties === null) {
			$table = $this->game->getDb() . '.partei';
			$sql   = "SELECT name, beschreibung AS description FROM " . $table . " WHERE id NOT IN ('0', 'dark', 'tier') ORDER BY name";
			$stmt  = $this->connection->prepare($sql);
			$stmt->execute();
			$this->parties = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
		return $this->parties;
	}

	/**
	 * @return int
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getPartiesCount(): int {
		return count($this->getParties());
	}

	/**
	 * @return array[]
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getPartyRaces(): array {
		$table = $this->game->getDb() . '.partei';
		$sql   = "SELECT rasse AS race, COUNT(*) AS count FROM " . $table . " WHERE id NOT IN ('0', 'dark', 'tier') GROUP BY rasse ORDER BY rasse";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @return array[]
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getNewParties(): array {
		if ($this->newParties === null) {
			$table = $this->game->getDb() . '.neuespieler';
			$sql   = "SELECT name, description FROM " . $table . " ORDER BY name";
			$stmt  = $this->connection->prepare($sql);
			$stmt->execute();
			$this->newParties = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
		return $this->newParties;
	}

	/**
	 * @return int
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getNewPartiesCount(): int {
		return count($this->getNewParties());
	}

	/**
	 * @return array
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getLandscape(): array {
		$table = $this->game->getDb() . '.regionen';
		$sql   = "SELECT COUNT(*) FROM " . $table . " GROUP BY welt ORDER BY welt DESC";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
		return ['world' => $result[0], 'underworld' => $result[1]];
	}

	/**
	 * @return array[]
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getWorld(): array {
		$table = $this->game->getDb() . '.regionen';
		$sql   = "SELECT typ, COUNT(*) AS count FROM " . $table . " WHERE welt = 1 GROUP BY typ ORDER BY typ";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @return array[]
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getUnderworld(): array {
		$table = $this->game->getDb() . '.regionen';
		$sql   = "SELECT typ, COUNT(*) AS count FROM " . $table . " WHERE welt = -1 GROUP BY typ ORDER BY typ";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @return array
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getPopulation(): array {
		$table = $this->game->getDb() . '.einheiten';
		$sql   = "SELECT COUNT(*) AS units, SUM(person) AS persons FROM " . $table . " GROUP BY welt ORDER BY welt DESC";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		return $result[0];
	}

	/**
	 * @return array[]
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getRaces(): array {
		$table = $this->game->getDb() . '.einheiten';
		$sql   = "SELECT rasse AS race, COUNT(*) AS units, SUM(person) AS persons FROM " . $table . " WHERE partei NOT IN (620480, 1376883) GROUP BY rasse ORDER BY rasse";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @return array[]
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function getMonsters(): array {
		$table = $this->game->getDb() . '.einheiten';
		$sql   = "SELECT rasse AS race, COUNT(*) AS units, SUM(person) AS persons FROM " . $table . " WHERE partei IN (620480, 1376883) GROUP BY rasse ORDER BY rasse";
		$stmt  = $this->connection->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getCount() {
		$count      = DB::connection($game->database)->table('partei')->count();
		$parties    = DB::connection($game->database)->table('partei')->select(DB::raw('rasse, COUNT(*) AS count'))->groupBy('rasse')->get();
		$names      = DB::connection($game->database)->table('partei')->select('name', 'beschreibung')->orderBy('name')->get();
	}
}

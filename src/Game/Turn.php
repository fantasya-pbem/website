<?php
declare (strict_types = 1);
namespace App\Game;

use Doctrine\DBAL\Connection;

use App\Entity\Game;

/**
 * A helper class for game turn data.
 */
class Turn
{
	/**
	 * @var string
	 */
	private static $dateBase = '2017-12-31';

	/**
	 * @var Game
	 */
	private $game;

	/**
	 * @var int
	 */
	private $round;

	/**
	 * @var \DateTime
	 */
	private $lastZat;

	/**
	 * @param Game $game
	 * @return \DateTime
	 */
	public static function createStart(Game $game): \DateTime {
		$date = new \DateTime(self::$dateBase);
		$days = new \DateInterval('P' . $game->getStartDay() . 'DT' . $game->getStartHour() . 'H');
		return $date->add($days);
	}

	/**
	 * @param Game $game
	 * @param Connection $connection
	 */
	public function __construct(Game $game, Connection $connection) {
		$this->game = $game;
		$this->fetchData($connection);
	}

	/**
	 * @return int
	 */
	public function getRound(): int {
		return $this->round;
	}

	/**
	 * @return string
	 */
	public function getStart(): string {
		return $this->getDateString($this->lastZat);
	}

	/**
	 * @return string
	 */
	public function getNext(): string {
		$start    = new \DateTime($this->lastZat->format('Y-m-d H:i:s'));
		$lastDay  = (int)$start->format('N');
		$lastHour = (int)$start->format('G');
		$day      = $this->game->getStartDay();
		$hour     = $this->game->getStartHour();
		$start->setTime($hour, 0);
		if ($day > 0) {
			if ($lastDay > $day || $lastDay === $day && $lastHour >= $hour) {
				$start->add(new \DateInterval('P' . (7 - $lastDay + $day) . 'D'));
			} elseif ($lastDay < $day) {
				$start->add(new \DateInterval('P' . ($day - $lastDay) . 'D'));
			}
		} else {
			if ($lastHour >= $hour) {
				$start->add(new \DateInterval('P1D'));
			}
		}
		return $this->getDateString($start);
	}

	/**
	 * @param Connection $connection
	 */
	private function fetchData(Connection $connection) {
		$table = $this->game->getDb() . '.settings';
		$sql   = "SELECT value FROM " . $table . " WHERE name = 'game.runde'";
		$stmt  = $connection->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
		$this->round = (int)($result[0] ?? 0);

		$table = $this->game->getDb() . '.meldungen';
		$sql   = "SELECT MAX(zeit) FROM " . $table;
		$stmt  = $connection->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
		$this->lastZat = new \DateTime($result[0] ?? 'now');
	}

	/**
	 * @param \DateTime $date
	 * @return string
	 */
	private function getDateString(\DateTime $date): string {
		setlocale(LC_TIME, 'de_DE.utf8');
		return strftime('%A, %e. %B %Y, %k:%M Uhr', $date->getTimestamp());
	}
}

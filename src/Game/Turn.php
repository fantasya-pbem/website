<?php
declare(strict_types = 1);
namespace App\Game;

use App\Service\EngineService;

use App\Entity\Game;

class Turn
{
	private const string DATETIME_FORMAT = "EEEE, d. MMMM yyyy, k:mm 'Uhr'";

	private static string $dateBase = '2017-12-31';

	private int $round;

	private \DateTime $lastZat;

	public static function createStart(Game $game): \DateTime {
		$date = new \DateTime(self::$dateBase);
		$days = new \DateInterval('P' . $game->getStartDay() . 'DT' . $game->getStartHour() . 'H');
		return $date->add($days);
	}

	/**
	 * @throws \Exception
	 */
	public function __construct(private readonly Game $game, private readonly EngineService $engineService) {
		$this->fetchData();
	}

	public function getRound(): int {
		return $this->round;
	}

	public function getStart(): string {
		return Game::dateFormat(self::DATETIME_FORMAT)->format($this->lastZat);
	}

	public function getNext(): string {
		$start    = new \DateTime($this->lastZat->format('Y-m-d H:i:s'));
		$lastDay  = (int)$start->format('N');
		$lastHour = (int)$start->format('G');
		$day      = $this->game->getStartDay();
		$hour     = $this->game->getStartHour();
		$start->setTime($hour, 0);
		if ($day >= 10) {
			$start->add(new \DateInterval('P1M'));
			$day   = (int)($day / 10);
			$month = (int)$start->format('m');
			$year  = (int)$start->format('Y');
			$start->setDate($year, $month, $day);
		} elseif ($day > 0) {
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
		return Game::dateFormat(self::DATETIME_FORMAT)->format($start);
	}

	/**
	 * @throws \Exception
	 */
	private function fetchData(): void {
		$engine        = $this->engineService->get($this->game);
		$this->round   = $engine->getRound($this->game);
		$this->lastZat = $engine->getLastZat($this->game);
	}
}

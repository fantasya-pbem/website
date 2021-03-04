<?php
declare (strict_types = 1);
namespace App\Service;

use JetBrains\PhpStorm\Pure;

use App\Data\Report;

/**
 * A service for fetching Fantasya report files.
 */
class ReportService
{
	private string $baseDir;

	private Report $report;

	public function __construct() {
		$this->baseDir = realpath(__DIR__ . '/../../var/zip');
		if (!$this->baseDir) {
			throw new \RuntimeException('Reports directory not found.');
		}
	}

	#[Pure] public function getPath(): string {
		return $this->baseDir . DIRECTORY_SEPARATOR . $this->report->getGame() . DIRECTORY_SEPARATOR .
			   $this->report->getTurn() . DIRECTORY_SEPARATOR .
			   $this->report->getTurn() . '-' . $this->report->getParty() . '.zip';
	}

	/**
	 * @return int[]
	 */
	public function getTurns(): array {
		$turns   = [];
		$pattern = $this->baseDir . DIRECTORY_SEPARATOR . $this->report->getGame() . DIRECTORY_SEPARATOR . '*';
		foreach (glob($pattern) as $roundDir) {
			$round = basename($roundDir);
			$turn  = (int)$round;
			if ((string)$turn === $round) {
				$this->report->setTurn($turn);
				if (is_file($this->getPath())) {
					$turns[$turn] = $turn;
				}
			}
		}
		return $turns;
	}

	public function setContext(Report $report): void {
		$this->report = $report;
	}
}

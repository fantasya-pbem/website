<?php
declare (strict_types = 1);
namespace App\Service;

use App\Data\Report;

/**
 * A service for fetching Fantasya report files.
 */
class ReportService
{
	/**
	 * @var string
	 */
	private $baseDir;

	/**
	 * @var Report
	 */
	private $report;

	/**
	 * Initialize order base directory.
	 */
	public function __construct() {
		$this->baseDir = realpath(__DIR__ . '/../../var/zip');
		if (!$this->baseDir) {
			throw new \RuntimeException('Reports directory not found.');
		}
	}

	/**
	 * @return string
	 */
	public function getPath(): string {
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

	/**
	 * @param Report $report
	 */
	public function setContext(Report $report) {
		$this->report = $report;
	}
}

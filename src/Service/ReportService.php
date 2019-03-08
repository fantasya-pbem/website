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
	 * @return string
	 */
	public function getZip(): string {
		$file = $this->getPath();
		if (is_file($file)) {
			$contents = file_get_contents($file);
			if ($contents) {
				return $contents;
			}
		}
		return '';
	}

	/**
	 * @param Report $report
	 */
	public function setContext(Report $report) {
		$this->report = $report;
	}
}

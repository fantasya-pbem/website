<?php
declare(strict_types = 1);
namespace App\Service;

use App\Data\Report;
use App\Data\Reports;
use App\Game\Engine\Lemuria;

class ReportService
{
	private string $baseDir;

	private string $turnDir;

	private Report $report;

	public function __construct(private readonly EngineService $engineService, private readonly TempDirService $tempDirService) {
		$this->baseDir = realpath(__DIR__ . '/../../var/zip');
		$this->turnDir = realpath(__DIR__ . '/../../var/reports');
		if (!$this->baseDir || !$this->turnDir) {
			throw new \RuntimeException('Reports directory not found.');
		}
	}

	public function getPath(): string {
		$engine = $this->engineService->get($this->report->getGame());
		if ($engine instanceof Lemuria) {
			$tempDir  = $this->tempDirService->create('lemuria_');
			$fileName = $this->report->getTurn() . '-' . $this->report->getParty() . '.zip';
			$path     = $tempDir . DIRECTORY_SEPARATOR . $fileName;
			$this->createZip($path);
			return $path;
		}

		return $this->baseDir . DIRECTORY_SEPARATOR . $this->report->getGame()->getAlias() .
			   DIRECTORY_SEPARATOR . $this->report->getTurn() . DIRECTORY_SEPARATOR .
			   $this->report->getTurn() . '-' . $this->report->getParty() . '.zip';
	}

	/**
	 * @return array<int>
	 */
	public function getTurns(): array {
		$engine = $this->engineService->get($this->report->getGame());
		if ($engine instanceof Lemuria) {
			$party = $engine->getById($this->report->getParty(), $this->report->getGame());
			$first = $party->getRound();
			$last  = $engine->getRound($this->report->getGame());
			$turns = array_keys(array_fill($first, $last - $first + 1, null));
			return array_combine($turns, $turns);
		}

		$turns   = [];
		$pattern = $this->baseDir . DIRECTORY_SEPARATOR . $this->report->getGame()->getAlias() . DIRECTORY_SEPARATOR . '*';
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
		asort($turns);
		return $turns;
	}

	public function setContext(Report $report): void {
		$this->report = $report;
	}

	protected function createZip(string $path): void {
		$party   = $this->report->getParty();
		$alias   = $this->report->getGame()->getAlias();
		$turn    = $this->report->getTurn();
		$reports = new Reports($this->report->getUser()->getFlags());

		$zip = new \ZipArchive();
		$zip->open($path, \ZipArchive::CREATE);
		$turnDir = $this->turnDir . DIRECTORY_SEPARATOR .  $alias . DIRECTORY_SEPARATOR . $turn;
		if (!is_dir($turnDir)) {
			$year    = 'year.' . (max(0, (int)(($this->report->getTurn() - 1) / 24) + 1));
			$turnDir = $this->turnDir . DIRECTORY_SEPARATOR .  $alias . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $turn;
			if (!is_dir($turnDir)) {
				throw new \RuntimeException('Turn directory not found.');
			}
		}
		foreach (glob($turnDir . DIRECTORY_SEPARATOR . $party . '.*') as $file) {
			$name      = basename($file);
			$extension = substr($name, strrpos($name, '.') + 1);
			$control   = $party . '.' . $extension;
			if ($name === $control && !$reports->byExtension($extension)) {
				continue;
			}
			if (preg_match('/^' . $party . '\.[a-z]+\.txt$/', $name) === 1 && !$reports->allowAdditionalFiles()) {
				continue;
			}
			$zip->addFile($file, $name);
		}
		$zip->close();
	}
}

<?php
declare(strict_types = 1);
namespace App\Game\Engine;

use JetBrains\PhpStorm\ArrayShape;

use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Engine\Fantasya\Factory\Model\LemuriaNewcomer;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Commodity\Griffin;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Monster;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Party\Type;

use App\Game\Statistics;

class LemuriaStatistics implements Statistics
{
	use BuilderTrait;
	use GrammarTrait;

	/**
	 * @var array<array>|null
	 */
	private ?array $parties = null;

	/**
	 * @var array<array>|null
	 */
	private ?array $newbies = null;

	/**
	 * @var array<array>
	 */
	private array $races = [];

	/**
	 * @var array<array>|null
	 */
	private ?array $landscape = null;

	private int $regions = 0;

	private ?array $population = null;

	private ?array $mosters = null;

	/**
	 * @var array<array>|null
	 */
	private ?array $persons = null;

	public function __construct() {
	}

	/**
	 * @return array<array>
	 */
	public function getParties(): array {
		if ($this->parties === null) {
			$this->parties = [];
			$races         = [];
			foreach (Lemuria::Catalog()->getAll(Domain::Party) as $party /* @var Party $party */) {
				if ($party->Type() !== Type::Player || $party->hasRetired()) {
					continue;
				}
				$name                 = $party->Name();
				$this->parties[$name] = ['name' => $name, 'description' => $party->Description()];

				$race = (string)$party->Race();
				if (!isset($races[$race])) {
					$races[$race] = 0;
				}
				$races[$race]++;
			}
			ksort($this->parties);
			$this->parties = array_values($this->parties);
			foreach ($this->sortAndTranslate($races) as $race => $count) {
				$this->races[] = ['race' => $race, 'count' => $count];
			}
		}
		return $this->parties;
	}

	public function getPartiesCount(): int {
		return count($this->getParties());
	}

	/**
	 * @return array<array>
	 */
	public function getPartyRaces(): array {
		return $this->races;
	}

	/**
	 * @return array<array>
	 */
	public function getNewbies(): array {
		if (!$this->newbies) {
			$this->newbies = [];
			foreach (Lemuria::Debut()->getAll() as $newcomer /* @var LemuriaNewcomer $newcomer */) {
				$this->newbies[] = ['name' => $newcomer->Name(), 'description' => $newcomer->Description()];
			}
		}
		return $this->newbies;
	}

	public function getNewbiesCount(): int {
		return count($this->getNewbies());
	}

	#[ArrayShape(['world' => "array|mixed", 'underworld' => "array|mixed"])]
	public function getLandscape(): array {
		if (!$this->landscape) {
			$this->landscape = [];
			$regions         = [];
			foreach (Lemuria::Catalog()->getAll(Domain::Location) as $region /* @var Region $region */) {
				$landscape = (string)$region->Landscape();
				if (!isset($regions[$landscape])) {
					$regions[$landscape] = 0;
				}
				$regions[$landscape]++;
				$this->regions++;
			}
			foreach ($this->sortAndTranslate($regions) as $landscape => $count) {
				$this->landscape[] = ['typ' => $landscape, 'count' => $count];
			}
		}
		return ['world' => $this->regions, 'underworld' => 0];
	}

	/**
	 * @return array<array>
	 */
	public function getWorld(): array {
		return $this->landscape;
	}

	/**
	 * @return array<array>
	 */
	public function getUnderworld(): array {
		return [];
	}

	public function getPopulation(): array {
		if (!$this->population) {
			$this->persons = [];
			$persons       = [];
			$this->mosters = [];
			$monsters      = [];
			$count         = 0;
			$total         = 0;
			$this->getUnitPopulation($persons, $monsters, $count, $total);
			$this->getResourcesPopulation($monsters, $count, $total);
			foreach ($this->sortAndTranslate($persons) as $race => $numbers) {
				$this->persons[] = ['race' => $race, 'units' => $numbers[0], 'persons' => $numbers[1]];
			}
			foreach ($this->sortAndTranslate($monsters) as $race => $numbers) {
				$this->mosters[] = ['race' => $race, 'units' => $numbers[0], 'persons' => $numbers[1]];
			}
			$this->population = ['units' => $count, 'persons' => $total];
		}
		return $this->population;
	}

	/**
	 * @return array<array>
	 */
	public function getRaces(): array {
		return $this->persons;
	}

	/**
	 * @return array<array>
	 */
	public function getMonsters(): array {
		return $this->mosters;
	}

	protected function getUnitPopulation(array &$persons, array &$monsters, int &$count, int &$total): void {
		foreach (Lemuria::Catalog()->getAll(Domain::Unit) as $unit) {
			$race = $unit->Race();
			$r    = (string)$unit->Race();
			$size = $unit->Size();

			if ($race instanceof Monster) {
				if (!isset($monsters[$r])) {
					$monsters[$r] = [0, 0];
				}
				$monsters[$r][0]++;
				$monsters[$r][1] += $size;
			} else {
				if (!isset($persons[$r])) {
					$persons[$r] = [0, 0];
				}
				$persons[$r][0]++;
				$persons[$r][1] += $size;
			}
			$total += $size;
			$count++;
		}
	}

	protected function getResourcesPopulation(array &$monsters, int &$count, int &$total): void {
		$griffin      = self::createCommodity(Griffin::class);
		$griffinCount = 0;
		$griffinSize  = 0;
		foreach (Lemuria::Catalog()->getAll(Domain::Location) as $region) {
			$resources = $region->Resources();
			/** @noinspection PhpIllegalArrayKeyTypeInspection */
			$griffins = $resources[$griffin]->Count();
			if ($griffins > 0) {
				$griffinCount++;
				$count++;
				$griffinSize += $griffins;
				$total       += $griffins;
			}
		}
		if ($griffinCount) {
			$monsters[(string)$griffin] = [$griffinCount, $griffinSize];
		}
	}

	protected function sortAndTranslate(array $numbers): array {
		$sorted = [];
		foreach ($numbers as $singleton => $count) {
			$sorted[$this->translateSingleton($singleton)] = $count;
		}
		ksort($sorted);
		return $sorted;
	}
}

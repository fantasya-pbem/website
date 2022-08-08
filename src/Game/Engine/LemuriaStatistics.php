<?php
declare (strict_types = 1);
namespace App\Game\Engine;

use JetBrains\PhpStorm\ArrayShape;

use Lemuria\Engine\Fantasya\Factory\Model\LemuriaNewcomer;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Commodity\Griffin;
use Lemuria\Model\Fantasya\Factory\BuilderTrait;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Unit;

use App\Game\Statistics;

class LemuriaStatistics implements Statistics
{
	use BuilderTrait;

	/**
	 * @var array[]|null
	 */
	private ?array $parties = null;

	/**
	 * @var array[]|null
	 */
	private ?array $newbies = null;

	/**
	 * @var array[]
	 */
	private array $races = [];

	/**
	 * @var array[]|null
	 */
	private ?array $landscape = null;

	private int $regions = 0;

	private ?array $population = null;

	private ?array $mosters = null;

	/**
	 * @var array[]|null
	 */
	private ?array $persons = null;

	private Dictionary $dictionary;

	public function __construct() {
		$this->dictionary = new Dictionary();
	}

	/**
	 * @return array[]
	 */
	public function getParties(): array {
		if ($this->parties === null) {
			$this->parties = [];
			$races         = [];
			foreach (Lemuria::Catalog()->getAll(Domain::PARTY) as $party /* @var Party $party */) {
				if ($party->Type() !== Type::PLAYER) {
					continue;
				}
				$this->parties[] = ['name' => $party->Name(), 'description' => $party->Description()];

				$race = (string)$party->Race();
				if (!isset($races[$race])) {
					$races[$race] = 0;
				}
				$races[$race]++;
			}
			foreach ($races as $race => $count) {
				$this->races[] = ['race' => $this->dictionary->get('race', $race), 'count' => $count];
			}
		}
		return $this->parties;
	}

	public function getPartiesCount(): int {
		return count($this->getParties());
	}

	/**
	 * @return array[]
	 */
	public function getPartyRaces(): array {
		return $this->races;
	}

	/**
	 * @return array[]
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
			foreach (Lemuria::Catalog()->getAll(Domain::LOCATION) as $region /* @var Region $region */) {
				$landscape = (string)$region->Landscape();
				if (!isset($regions[$landscape])) {
					$regions[$landscape] = 0;
				}
				$regions[$landscape]++;
				$this->regions++;
			}
			foreach ($regions as $landscape => $count) {
				$this->landscape[] = ['typ' => $this->dictionary->get('landscape', $landscape), 'count' => $count];
			}
		}
		return ['world' => $this->regions, 'underworld' => 0];
	}

	/**
	 * @return array[]
	 */
	public function getWorld(): array {
		return $this->landscape;
	}

	/**
	 * @return array[]
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
			foreach ($persons as $race => $numbers) {
				$this->persons[] = ['race' => $this->dictionary->get('race', $race), 'units' => $numbers[0], 'persons' => $numbers[1]];
			}
			foreach ($monsters as $race => $numbers) {
				$this->mosters[] = ['race' => $this->dictionary->get('race', $race), 'units' => $numbers[0], 'persons' => $numbers[1]];
			}
			$this->population = ['units' => $count, 'persons' => $total];
		}
		return $this->population;
	}

	/**
	 * @return array[]
	 */
	public function getRaces(): array {
		return $this->persons;
	}

	/**
	 * @return array[]
	 */
	public function getMonsters(): array {
		return $this->mosters;
	}

	protected function getUnitPopulation(array &$persons, array &$monsters, int &$count, int &$total): void {
		foreach (Lemuria::Catalog()->getAll(Domain::UNIT) as $unit /* @var Unit $unit */) {
			$type = $unit->Party()->Type();
			$race = (string)$unit->Race();
			$size = $unit->Size();

			if ($type === Type::PLAYER) {
				if (!isset($persons[$race])) {
					$persons[$race] = [0, 0];
				}
				$persons[$race][0]++;
				$persons[$race][1] += $size;
			} elseif ($type === Type::MONSTER) {
				if (!isset($monsters[$race])) {
					$monsters[$race] = [0, 0];
				}
				$monsters[$race][0]++;
				$monsters[$race][1] += $size;
			}
			$total += $size;
			$count++;
		}
	}

	protected function getResourcesPopulation(array &$monsters, int &$count, int &$total): void {
		$griffin      = self::createCommodity(Griffin::class);
		$griffinCount = 0;
		$griffinSize  = 0;
		foreach (Lemuria::Catalog()->getAll(Domain::LOCATION) as $region /* @var Region $region */) {
			$resources = $region->Resources();
			$griffins  = $resources[$griffin]->Count();
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
}

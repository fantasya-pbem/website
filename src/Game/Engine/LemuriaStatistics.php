<?php
declare (strict_types = 1);
namespace App\Game\Engine;

use JetBrains\PhpStorm\ArrayShape;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Model\Lemuria\Region;
use Lemuria\Model\Lemuria\Unit;

use App\Entity\Assignment;
use App\Entity\Game;
use App\Game\Newbie;
use App\Game\Statistics;
use App\Repository\AssignmentRepository;

class LemuriaStatistics implements Statistics
{
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

	/**
	 * @var array[]|null
	 */
	private ?array $persons = null;

	public function __construct(private Game $game, private AssignmentRepository $assignmentRepository) {
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getParties(): array {
		if ($this->parties === null) {
			$this->parties = [];
			$races         = [];
			foreach (Lemuria::Catalog()->getAll(Catalog::PARTIES) as $party /* @var Party $party */) {
				$this->parties[] = ['name' => $party->Name(), 'description' => $party->Description()];

				$race = (string)$party->Race();
				if (!isset($races[$race])) {
					$races[$race] = 0;
				}
				$races[$race]++;
			}
			foreach ($races as $race => $count) {
				$this->races[] = ['race' => $race, 'count' => $count];
			}
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
		return $this->races;
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getNewbies(): array {
		if (!$this->newbies) {
			$this->newbies = [];
			foreach ($this->assignmentRepository->findAll() as $assignment /* @var Assignment $assignment */) {
				if (str_starts_with($assignment->getUuid(), 'newbie-')) {
					$newbie          = Newbie::fromAssignment($assignment);
					$this->newbies[] = ['name' => $newbie->getName(), 'description' => $newbie->getDescription()];
				}
			}
		}
		return $this->newbies;
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
		if (!$this->landscape) {
			$this->landscape = [];
			$regions         = [];
			foreach (Lemuria::Catalog()->getAll(Catalog::LOCATIONS) as $region /* @var Region $region */) {
				$landscape = (string)$region->Landscape();
				if (!isset($regions[$landscape])) {
					$regions[$landscape] = 0;
				}
				$regions[$landscape]++;
				$this->regions++;
			}
			foreach ($regions as $landscape => $count) {
				$this->landscape[] = ['typ' => $landscape, 'count' => $count];
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

	/**
	 * @throws \Exception
	 */
	public function getPopulation(): array {
		if (!$this->population) {
			$this->persons = [];
			$persons       = [];
			$count         = 0;
			$total         = 0;
			foreach (Lemuria::Catalog()->getAll(Catalog::UNITS) as $unit /* @var Unit $unit*/) {
				$size = $unit->Size();
				$race = (string)$unit->Race();
				if (!isset($persons[$race])) {
					$persons[$race] = [0, 0];
				}
				$persons[$race][0]++;
				$persons[$race][1] += $size;
				$total             += $size;
				$count++;
			}
			foreach ($persons as $race => $numbers) {
				$this->persons[] = ['race' => $race, 'units' => $numbers[0], 'persons' => $numbers[1]];
			}
			$this->population = ['units' => $count, 'persons' => $total];
		}
		return $this->population;
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getRaces(): array {
		return $this->persons;
	}

	/**
	 * @return array[]
	 */
	public function getMonsters(): array {
		return [];
	}
}

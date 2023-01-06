<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

use App\Game\Turn;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: GameRepository::class)]
class Game
{
	#[Column]
	#[GeneratedValue]
	#[Id]
	private ?int $id = null;

	#[Column(length: 190, unique: true)]
	private string $name = '';

	#[Column(type: 'text')]
	private string $description = '';

	#[Column(length: 8)]
	private string $engine = '';

	#[Column(length: 32, unique: true)]
	private string $db = '';

	#[Column(length: 32, unique: true)]
	private string $alias = '';

	#[Column]
	private bool $is_active = false;

	#[Column]
	private bool $can_enter = false;

	#[Column(type: 'smallint')]
	private int $start_day = 0;

	#[Column(type: 'smallint')]
	private int $start_hour = 0;

	public static function dateFormat(string $pattern = ''): \IntlDateFormatter {
		return new \IntlDateFormatter('de-DE', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT, pattern: $pattern);
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function setDescription(string $description): self {
		$this->description = $description;
		return $this;
	}

	public function getEngine(): string {
		return $this->engine;
	}

	public function setEngine(string $engine): self {
		$this->engine = $engine;
		return $this;
	}

	public function getDb(): string {
		return $this->db;
	}

	public function setDb(string $db): self {
		$this->db = $db;
		return $this;
	}

	public function getAlias(): string {
		return $this->alias;
	}

	public function setAlias(string $alias): self {
		$this->alias = $alias;
		return $this;
	}

	public function getIsActive(): bool {
		return $this->is_active;
	}

	public function setIsActive(bool $is_active): self {
		$this->is_active = $is_active;
		return $this;
	}

	public function getCanEnter(): bool {
		return $this->can_enter;
	}

	public function setCanEnter(bool $can_enter): self {
		$this->can_enter = $can_enter;
		return $this;
	}

	public function getStartDay(): int {
		return $this->start_day;
	}

	public function setStartDay(int $start_day): self {
		$this->start_day = $start_day;
		return $this;
	}

	public function getStartHour(): int {
		return $this->start_hour;
	}

	public function setStartHour(int $start_hour): self {
		$this->start_hour = $start_hour;
		return $this;
	}

	public function getStart(): string {
		if ($this->start_day > 0) {
			$start = Turn::createStart($this)->getTimestamp();
			return self::dateFormat("EEEE, k 'Uhr'")->format($start);
		}
		return 'täglich, ' . $this->start_hour . ' Uhr';
	}
}

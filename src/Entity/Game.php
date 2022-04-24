<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Game\Turn;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private ?int $id = null;

	/**
	 * @ORM\Column(type="string", length=190, unique=true)
	 */
	private string $name = '';

	/**
	 * @ORM\Column(type="text")
	 */
	private string $description = '';

	/**
	 * @ORM\Column(type="string", length=8)
	 */
	private string $engine = '';

	/**
	 * @ORM\Column(type="string", length=32, unique=true)
	 */
	private string $db = '';

	/**
	 * @ORM\Column(type="string", length=32, unique=true)
	 */
	private string $alias = '';

	/**
	 * @ORM\Column(type="boolean")
	 */
	private bool $is_active = false;

	/**
	 * @ORM\Column(type="smallint")
	 */
	private int $start_day = 0;

	/**
	 * @ORM\Column(type="smallint")
	 */
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

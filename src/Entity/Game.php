<?php
declare (strict_types=1);
namespace App\Entity;

use App\Game\Turn;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 *
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=190, unique=true)
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="text")
	 *
	 * @var string
	 */
	private $description;

	/**
	 * @ORM\Column(type="string", length=32, unique=true)
	 *
	 * @var string
	 */
	private $db;

	/**
	 * @ORM\Column(type="string", length=32, unique=true)
	 *
	 * @var string
	 */
	private $alias;

	/**
	 * @ORM\Column(type="boolean")
	 *
	 * @var bool
	 */
	private $is_active;

	/**
	 * @ORM\Column(type="smallint")
	 *
	 * @var int
	 */
	private $start_day;

	/**
	 * @ORM\Column(type="smallint")
	 *
	 * @var int
	 */
	private $start_hour;

	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Game
	 */
	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string {
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return Game
	 */
	public function setDescription(string $description): self {
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getDb(): ?string {
		return $this->db;
	}

	/**
	 * @param string $db
	 * @return Game
	 */
	public function setDb(string $db): self {
		$this->db = $db;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getAlias(): ?string {
		return $this->alias;
	}

	/**
	 * @param string $alias
	 * @return Game
	 */
	public function setAlias(string $alias): self {
		$this->alias = $alias;
		return $this;
	}

	/**
	 * @return bool|null
	 */
	public function getIsActive(): ?bool {
		return $this->is_active;
	}

	/**
	 * @param bool $is_active
	 * @return Game
	 */
	public function setIsActive(bool $is_active): self {
		$this->is_active = $is_active;
		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getStartDay(): ?int {
		return $this->start_day;
	}

	/**
	 * @param int $start_day
	 * @return Game
	 */
	public function setStartDay(int $start_day): self {
		$this->start_day = $start_day;
		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getStartHour(): ?int {
		return $this->start_hour;
	}

	/**
	 * @param int $start_hour
	 * @return Game
	 */
	public function setStartHour(int $start_hour): self {
		$this->start_hour = $start_hour;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStart(): string {
		if ($this->start_day > 0) {
			$start = Turn::createStart($this)->getTimestamp();
			setlocale(LC_TIME, 'de_DE.utf8');
			return strftime('%A, %H Uhr', $start);
		}
		return 'täglich, ' . $this->start_hour . ' Uhr';
	}
}

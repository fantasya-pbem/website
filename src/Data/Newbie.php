<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Newbie
{
	/**
	 * @Assert\NotBlank
	 * @var string
	 */
	private $name = '';

	/**
	 * @var string
	 */
	private $description = '';

	/**
	 * @Assert\Choice({"Aquaner", "Elf", "Halbling", "Mensch", "Ork", "Troll", "Zwerg"})
	 * @var string
	 */
	private $race = '';

	/**
	 * @Assert\Range(min = 0, max = 90)
	 * @var int
	 */
	private $wood = 0;

	/**
	 * @Assert\Range(min = 0, max = 90)
	 * @var int
	 */
	private $stone = 0;

	/**
	 * @Assert\Range(min = 0, max = 90)
	 * @var int
	 */
	private $iron = 0;

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getRace(): string {
		return $this->race;
	}

	/**
	 * @return int
	 */
	public function getWood(): int {
		return $this->wood;
	}

	/**
	 * @return int
	 */
	public function getStone(): int {
		return $this->stone;
	}

	/**
	 * @return int
	 */
	public function getIron(): int {
		return $this->iron;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) {
		$this->name = $name;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description) {
		$this->description = $description;
	}

	/**
	 * @param string $race
	 */
	public function setRace(string $race) {
		$this->race = $race;
	}

	/**
	 * @param int $wood
	 */
	public function setWood(int $wood) {
		$this->wood = $wood;
	}

	/**
	 * @param int $stone
	 */
	public function setStone(int $stone) {
		$this->stone = $stone;
	}

	/**
	 * @param int $iron
	 */
	public function setIron(int $iron) {
		$this->iron = $iron;
	}

	/**
	 * @return int
	 */
	public function getResources(): int {
		return $this->wood + $this->stone + $this->iron;
	}
}

<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Newbie
{
	/**
	 * @Assert\NotBlank
	 */
	private string $name = '';

	private string $description = '';

	/**
	 * @Assert\Choice({"Aquaner", "Elf", "Halbling", "Mensch", "Ork", "Troll", "Zwerg"})
	 */
	private string $race = '';

	/**
	 * @Assert\Range(min = 0, max = 90)
	 */
	private int $wood = 0;

	/**
	 * @Assert\Range(min = 0, max = 90)
	 */
	private int $stone = 0;

	/**
	 * @Assert\Range(min = 0, max = 90)
	 */
	private int $iron = 0;

	public function getName(): string {
		return $this->name;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getRace(): string {
		return $this->race;
	}

	public function getWood(): int {
		return $this->wood;
	}

	public function getStone(): int {
		return $this->stone;
	}

	public function getIron(): int {
		return $this->iron;
	}

	public function setName(string $name): void {
		$this->name = $name;
	}

	public function setDescription(string $description): void {
		$this->description = $description;
		$this->cleanDescription();
	}

	public function setRace(string $race): void {
		$this->race = $race;
	}

	public function setWood(int $wood): void {
		$this->wood = $wood;
	}

	public function setStone(int $stone): void {
		$this->stone = $stone;
	}

	public function setIron(int $iron): void {
		$this->iron = $iron;
	}

	public function getResources(): int {
		return $this->wood + $this->stone + $this->iron;
	}

	private function cleanDescription(): void {
		$this->description = str_replace(["\e", "\f", "\r", "\v"], '', $this->description);
		$this->description = str_replace(["\t", "\n"], ' ', $this->description);
		$this->description = trim($this->description, "\"'`^°§$%&/()={[]}\\+*~#<>|,-;:_ ");
	}
}

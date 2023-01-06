<?php
declare(strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class Newbie
{
	#[NotBlank]
	private string $name = '';

	private string $description = '';

	#[Choice(['Aquaner', 'Elf', 'Halbling', 'Mensch', 'Ork', 'Troll', 'Zwerg'])]
	private string $race = '';

	public function getName(): string {
		return $this->name;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getRace(): string {
		return $this->race;
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

	private function cleanDescription(): void {
		$this->description = str_replace(["\e", "\f", "\r", "\v"], '', $this->description);
		$this->description = str_replace(["\t", "\n"], ' ', $this->description);
		$this->description = trim($this->description, "\"'`^°§$%&/()={[]}\\+*~#<>|,-;:_ ");
	}
}

<?php
declare(strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class Report
{
	#[NotBlank]
	private string $party = '';

	#[GreaterThanOrEqual(0)]
	private int $turn = 0;

	private string $game = '';

	public function getParty(): string {
		return $this->party;
	}

	public function getTurn(): int {
		return $this->turn;
	}

	public function getGame(): string {
		return $this->game;
	}

	public function setParty(string $party): void {
		$this->party = $party;
	}

	public function setTurn(int $turn): void {
		$this->turn = $turn;
	}

	public function setGame(string $game): void {
		$this->game = $game;
	}
}

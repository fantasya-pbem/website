<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Report
{
	/**
	 * @Assert\NotBlank
	 */
	private string $party = '';

	/**
	 * @Assert\GreaterThanOrEqual(0)
	 */
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

	public function setParty(string $party) {
		$this->party = $party;
	}

	public function setTurn(int $turn) {
		$this->turn = $turn;
	}

	public function setGame(string $game) {
		$this->game = $game;
	}
}

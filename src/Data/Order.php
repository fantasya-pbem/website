<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
	/**
	 * @Assert\GreaterThan(0)
	 */
	private int $party = 0;

	/**
	 * @Assert\GreaterThan(0)
	 */
	private int $turn = 0;

	private string $orders = '';

	private string $game = '';

	public function getParty(): int {
		return $this->party;
	}

	public function getTurn(): int {
		return $this->turn;
	}

	public function getOrders(): string {
		return $this->orders;
	}

	public function getGame(): string {
		return $this->game;
	}

	public function setParty(int $party) {
		$this->party = $party;
	}

	public function setTurn(int $turn) {
		$this->turn = $turn;
	}

	public function setOrders(string $orders) {
		$this->orders = $orders;
	}

	public function setGame(string $game) {
		$this->game = $game;
	}
}

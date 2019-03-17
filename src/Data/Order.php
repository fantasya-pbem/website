<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
	/**
	 * @Assert\GreaterThan(0)
	 * @var int
	 */
	private $party = 0;

	/**
	 * @Assert\GreaterThan(0)
	 * @var int
	 */
	private $turn = 0;

	/**
	 * @var string
	 */
	private $orders = '';

	/**
	 * @var string
	 */
	private $game = '';

	/**
	 * @return int
	 */
	public function getParty(): int {
		return $this->party;
	}

	/**
	 * @return int
	 */
	public function getTurn(): int {
		return $this->turn;
	}

	/**
	 * @return string
	 */
	public function getOrders(): string {
		return $this->orders;
	}

	/**
	 * @return string
	 */
	public function getGame(): string {
		return $this->game;
	}

	/**
	 * @param int $party
	 */
	public function setParty(int $party) {
		$this->party = $party;
	}

	/**
	 * @param int $turn
	 */
	public function setTurn(int $turn) {
		$this->turn = $turn;
	}

	/**
	 * @param string $orders
	 */
	public function setOrders(string $orders) {
		$this->orders = $orders;
	}

	/**
	 * @param string $game
	 */
	public function setGame(string $game) {
		$this->game = $game;
	}
}

<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Order
{
	/**
	 * @Assert\NotBlank
	 * @var string
	 */
	private $party = '';

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
	 * @return string
	 */
	public function getParty(): string {
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
	 * @param string $party
	 */
	public function setParty(string $party) {
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

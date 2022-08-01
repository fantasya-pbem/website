<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Game;

class Order
{
	private string $party = '';

	/**
	 * @Assert\GreaterThanOrEqual(0)
	 */
	private int $turn = 0;

	private string $orders = '';

	private Game $game;

	public function getParty(): string {
		return $this->party;
	}

	public function getTurn(): int {
		return $this->turn;
	}

	public function getOrders(): string {
		return $this->orders;
	}

	public function getGame(): Game {
		return $this->game;
	}

	public function getPartyId(): string {
		$orders = explode("\r\n", $this->orders);
		$line = explode(' ', strtolower(trim($orders[0] ?? '')));
		switch ($line[0]) {
			case 'eressea' :
			case 'fantasya' :
			case 'lemuria' :
			case 'partei' :
				return $this->parsePartyId($line);
			default :
				return '';
		}
	}

	public function setParty(string $party): void {
		$this->party = $party;
	}

	public function setTurn(int $turn): void {
		$this->turn = $turn;
	}

	public function setOrders(string $orders): void {
		$this->orders = $orders;
	}

	public function setGame(Game $game): void {
		$this->game = $game;
	}

	private function parsePartyId(array $line): string {
		for ($i = 1; $i < count($line); $i++) {
			$id = strtolower(trim($line[$i]));
			if (strlen($id) > 0) {
				return $id;
			}
		}
		return '';
	}
}

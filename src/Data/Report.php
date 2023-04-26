<?php
declare(strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Entity\Game;
use App\Entity\User;

class Report
{
	#[NotBlank]
	private string $party = '';

	#[GreaterThanOrEqual(0)]
	private int $turn = 0;

	private Game $game;

	private User $user;

	private string $path;

	public function getParty(): string {
		return $this->party;
	}

	public function getTurn(): int {
		return $this->turn;
	}

	public function getGame(): Game {
		return $this->game;
	}

	public function getUser(): User {
		return $this->user;
	}

	public function getPath(): string {
		return $this->path;
	}

	public function setParty(string $party): void {
		$this->party = $party;
	}

	public function setTurn(int $turn): void {
		$this->turn = $turn;
	}

	public function setGame(Game $game): void {
		$this->game = $game;
	}

	public function setUser(User $user): void {
		$this->user = $user;
	}

	public function setPath(string $path): void {
		$this->path = $path;
	}
}

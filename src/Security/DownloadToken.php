<?php
declare (strict_types = 1);
namespace App\Security;

use JetBrains\PhpStorm\Pure;

class DownloadToken
{
	protected const LENGTH = 16;

	private string $email = '';

	private int $game = 0;

	private int $party = 0;

	private int $turn = 0;

	public function __construct(private string $secret) {
	}

	public function setEmail(string $email): DownloadToken {
		$this->email = $email;
		return $this;
	}

	public function getGame(): int {
		return $this->game;
	}

	public function setGame(int $game): DownloadToken {
		$this->game = $game;
		return $this;
	}

	public function getParty(): int {
		return $this->party;
	}

	public function setParty(int $party): DownloadToken {
		$this->party = $party;
		return $this;
	}

	public function setTurn(int $turn): DownloadToken {
		$this->turn = $turn;
		return $this;
	}

	public function parse(string $token): DownloadToken {
		$idPart      = substr($token, self::LENGTH);
		$gameAndId   = hexdec($idPart);
		$this->game  = $gameAndId >> 24;
		$this->party = $gameAndId % 2 ** 24;
		return $this;
	}

	#[Pure] public function equals(DownloadToken $token): bool {
		return $token->__toString() === $this->__toString();
	}

	#[Pure] public function __toString(): string {
		return $this->generateToken();
	}

	#[Pure] protected function generateToken(): string {
		$data   = $this->email . $this->secret . $this->turn;
		$hash   = hash('sha256', $data);
		$idPart = dechex(2 ** 24 * $this->game + $this->party);
		return substr($hash, 0, self::LENGTH) . $idPart;
	}
}

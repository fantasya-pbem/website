<?php
declare (strict_types = 1);
namespace App\Security;

use JetBrains\PhpStorm\Pure;

class Token
{
	public const LENGTH = 16;

	private string $email = '';

	private int $turn = 0;

	public function __construct(private string $secret) {
	}

	public function setEmail(string $email): Token {
		$this->email = $email;
		return $this;
	}

	public function setTurn(int $turn): Token {
		$this->turn = $turn;
		return $this;
	}

	#[Pure] public function __toString(): string {
		return $this->generateToken();
	}

	#[Pure] protected function generateToken(): string {
		$data = $this->email . $this->secret . $this->turn;
		$hash = hash('sha256', $data);
		return substr($hash, 0, self::LENGTH);
	}
}

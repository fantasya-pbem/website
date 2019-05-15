<?php
declare (strict_types = 1);
namespace App\Security;

class Token
{
	const LENGTH = 16;

	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var string
	 */
	private $email = '';

	/**
	 * @var int
	 */
	private $turn = 0;

	/**
	 * Initialize secret.
	 */
	public function __construct() {
		$this->secret = getenv('APP_SECRET');
	}

	/**
	 * @param string $email
	 * @return Token
	 */
	public function setEmail(string $email): Token {
		$this->email = $email;
		return $this;
	}

	/**
	 * @param int $turn
	 * @return Token
	 */
	public function setTurn(int $turn): Token {
		$this->turn = $turn;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return $this->generateToken();
	}

	/**
	 * @return string
	 */
	protected function generateToken(): string {
		$data = $this->email . $this->secret . $this->turn;
		$hash = hash('sha256', $data);
		return substr($hash, 0, self::LENGTH);
	}
}

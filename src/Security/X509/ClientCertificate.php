<?php
declare(strict_types = 1);
namespace App\Security\X509;

use JetBrains\PhpStorm\Pure;

class ClientCertificate
{
	/**
	 * @type array<string, true>
	 */
	protected const array ALLOWED_CA = ['CAcert Inc.' => true, 'Root CA' => true];

	private string $ca;

	private int $serialNumber;

	private string $start;

	private string $end;

	private int $remainingDays;

	private string $email;

	private bool $isVerified;

	/**
	 * Parse client certificate data in request.
	 */
	#[Pure] public function __construct() {
		$this->ca            = $this->parse('I_DN_O');
		$this->serialNumber  = (int)hexdec($this->parse('M_SERIAL'));
		$this->start         = $this->parse('V_START');
		$this->end           = $this->parse('V_END');
		$this->remainingDays = (int)$this->parse('V_REMAIN');
		$this->email         = strtolower($this->parse('S_DN_Email'));
		$this->isVerified    = $this->parse('VERIFY') === 'SUCCESS';
	}

	public function getCA(): string {
		return $this->ca;
	}

	public function getSerialNumber(): int {
		return $this->serialNumber;
	}

	public function getStart(): string {
		return $this->start;
	}

	public function getEnd(): string {
		return $this->end;
	}

	public function getRemainingDays(): int {
		return $this->remainingDays;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function isVerified(): bool {
		return $this->isVerified;
	}

	public function isAllowed(): bool {
		return isset(self::ALLOWED_CA[$this->ca]);
	}

	public function isValid(): bool {
		return $this->isVerified() && $this->isAllowed() && $this->getRemainingDays() > 0;
	}

	protected function parse(string $key): string {
		return $_SERVER['REDIRECT_SSL_CLIENT_' . $key] ?? '';
	}
}

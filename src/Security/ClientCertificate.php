<?php
declare (strict_types = 1);
namespace App\Security;

class ClientCertificate
{
	protected const ALLOWED_CA = [
		'CAcert Inc.' => true,
		'Root CA'     => true
	];

	/**
	 * @var string
	 */
	private $ca;

	/**
	 * @var int
	 */
	private $serialNumber;

	/**
	 * @var string
	 */
	private $start;

	/**
	 * @var string
	 */
	private $end;

	/**
	 * @var int
	 */
	private $remainingDays;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var bool
	 */
	private $isVerified;

	/**
	 * Parse client certificate data in request.
	 */
	public function __construct() {
		$this->ca            = $this->parse('I_DN_O');
		$this->serialNumber  = (int)hexdec($this->parse('M_SERIAL'));
		$this->start         = $this->parse('V_START');
		$this->end           = $this->parse('V_END');
		$this->remainingDays = (int)$this->parse('V_REMAIN');
		$this->email         = strtolower($this->parse('S_DN_Email'));
		$this->isVerified    = $this->parse('VERIFY') === 'SUCCESS';
	}

	/**
	 * @return string
	 */
	public function getCA(): string {
		return $this->ca;
	}

	/**
	 * @return int
	 */
	public function getSerialNumber(): int {
		return $this->serialNumber;
	}

	/**
	 * @return string
	 */
	public function getStart(): string {
		return $this->start;
	}

	/**
	 * @return string
	 */
	public function getEnd(): string {
		return $this->end;
	}

	/**
	 * @return int
	 */
	public function getRemainingDays(): int {
		return $this->remainingDays;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * @return bool
	 */
	public function isVerified(): bool {
		return $this->isVerified;
	}

	/**
	 * @return bool
	 */
	public function isAllowed(): bool {
		return isset(self::ALLOWED_CA[$this->ca]);
	}

	/**
	 * @return bool
	 */
	public function isValid(): bool {
		return $this->isVerified() && $this->isAllowed() && $this->getRemainingDays() > 0;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function parse(string $key): string {
		return $_SERVER['REDIRECT_SSL_CLIENT_' . $key] ?? '';
	}
}

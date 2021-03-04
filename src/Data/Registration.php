<?php
declare (strict_types = 1);
namespace App\Data;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

class Registration extends PasswordReset
{
	/**
	 * @Assert\Expression("value in this.getValidAnswers()", message="Das ist nicht die richtige Antwort.")
	 */
	private string $antispam = '';

	/**
	 * @var string[]
	 */
	private array $validAnswers;

	#[Pure] public function __construct(string $answers) {
		$this->validAnswers = explode(',', $answers);
	}

	public function getAntispam(): string {
		return $this->antispam;
	}

	public function setAntispam(string $antispam) {
		$this->antispam = trim($antispam);
	}

	/**
	 * @return string[]
	 */
	public function getValidAnswers(): array {
		return $this->validAnswers;
	}
}

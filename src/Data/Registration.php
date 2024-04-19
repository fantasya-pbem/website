<?php
declare(strict_types = 1);
namespace App\Data;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints\Expression;

class Registration extends PasswordReset
{
	#[Expression('value in this.getValidAnswers()', 'Das ist nicht die richtige Antwort.')]
	private string $antispam = '';

	/**
	 * @var array<string>
	 */
	private array $validAnswers;

	#[Pure] public function __construct(string $answers) {
		$this->validAnswers = explode(',', $answers);
	}

	public function getAntispam(): string {
		return $this->antispam;
	}

	public function setAntispam(string $antispam): void {
		$this->antispam = trim($antispam);
	}

	/**
	 * @return array<string>
	 */
	public function getValidAnswers(): array {
		return $this->validAnswers;
	}
}

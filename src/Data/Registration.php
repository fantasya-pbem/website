<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Registration extends PasswordReset
{
	/**
	 * @Assert\Expression("value in this.getValidAnswers()", message="Das ist nicht die richtige Antwort.")
	 *
	 * @var string
	 */
	private $antispam = '';

	/**
	 * @var string[]
	 */
	private $validAnswers;

	/**
	 * @param string $answers
	 */
	public function __construct(string $answers) {
		$this->validAnswers = explode(',', $answers);
	}

	/**
	 * @return string
	 */
	public function getAntispam(): string {
		return $this->antispam;
	}

	/**
	 * @param string $antispam
	 */
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

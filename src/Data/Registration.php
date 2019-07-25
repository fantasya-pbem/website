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
	 * @return array
	 */
	public function getValidAnswers(): array {
		return explode(',', getenv('ANTISPAM_ANSWER'));
	}
}

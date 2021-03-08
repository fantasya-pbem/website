<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordReset
{
	/**
	 * @Assert\NotBlank
	 */
	private string $name = '';

	/**
	 * @Assert\Email
	 */
	private string $email = '';

	public function getName(): string {
		return $this->name;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function setName(string $name) {
		$this->name = $name;
	}

	public function setEmail(string $email) {
		$this->email = $email;
	}
}

<?php
declare(strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordReset
{
	#[NotBlank]
	private string $name = '';

	#[Email]
	private string $email = '';

	public function getName(): string {
		return $this->name;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function setName(string $name): void {
		$this->name = $name;
	}

	public function setEmail(string $email): void {
		$this->email = $email;
	}
}

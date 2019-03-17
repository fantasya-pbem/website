<?php
declare (strict_types = 1);
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordReset
{
	/**
	 * @Assert\NotBlank
	 *
	 * @var string
	 */
	private $name = '';

	/**
	 * @Assert\Email
	 *
	 * @var string
	 */
	private $email = '';

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) {
		$this->name = $name;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email) {
		$this->email = $email;
	}
}

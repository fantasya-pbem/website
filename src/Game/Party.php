<?php
declare (strict_types = 1);
namespace App\Game;

use JetBrains\PhpStorm\Pure;

class Party
{
	/**
	 * Convert Base-36 ID to numeric ID.
	 */
	#[Pure] public static function fromId(string $id): int {
		return (int)base_convert($id, 36, 10);
	}

	/**
	 * Convert numeric ID to Base-36 ID.
	 */
	#[Pure] public static function toId(int $id): string {
		return base_convert($id, 10, 36);
	}

	public function __construct(private array $properties) {
	}

	public function getId(): string {
		return $this->properties['id'];
	}

	public function getRace(): string {
		return $this->properties['rasse'];
	}

	public function getName(): string {
		return $this->properties['name'];
	}

	public function getDescription(): string {
		return $this->properties['beschreibung'];
	}

	public function getOwner(): string {
		return $this->properties['owner_id'];
	}

	public function getUser(): int {
		return (int)$this->properties['user_id'];
	}

	public function getEmail(): string {
		return $this->properties['email'];
	}
}

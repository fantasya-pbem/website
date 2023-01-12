<?php
declare(strict_types = 1);
namespace App\Game;

use JetBrains\PhpStorm\Pure;

use App\Data\Newbie as NewbieData;
use App\Entity\User;

class Newbie
{
	private string $uuid;

	private User $user;

	#[Pure] public static function fromData(NewbieData $data): self {
		return new self([
			'name'        => $data->getName(),
			'description' => $data->getDescription(),
			'rasse'       => $data->getRace(),
			'tarnung'     => '',
			'insel'       => 0,
			'password'    => ''
		]);
	}

	#[Pure] public function __construct(private array $properties) {
	}

	public function getRace(): string {
		return $this->properties['rasse'];
	}

	public function getName(): string {
		return $this->properties['name'];
	}

	public function getDescription(): string {
		return $this->properties['description'];
	}

	public function getUserId(): ?int {
		return $this->properties['user_id'] ?? null;
	}

	public function getProperties(): array {
		return $this->properties;
	}

	public function getUuid(): string {
		return $this->uuid;
	}

	public function getUser(): User {
		return $this->user;
	}

	public function setUser(User $user): self {
		$this->user = $user;

		$this->properties['email']   = $user->getEmail();
		$this->properties['user_id'] = $user->getId();
		return $this;
	}

	public function setUuid(string $uuid): self {
		$this->uuid = $uuid;
		return $this;
	}
}

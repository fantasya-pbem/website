<?php
declare (strict_types = 1);
namespace App\Game;

use JetBrains\PhpStorm\Pure;

use App\Data\Newbie as NewbieData;
use App\Entity\Assignment;
use App\Entity\User;

class Newbie
{
	private string $uuid;

	private User $user;

	public static function fromData(NewbieData $data): self {
		return new self([
			'name'        => $data->getName(),
			'description' => $data->getDescription(),
			'rasse'       => $data->getRace(),
			'holz'        => $data->getWood(),
			'steine'      => $data->getStone(),
			'eisen'       => $data->getIron(),
			'tarnung'     => '',
			'insel'       => 0,
			'password'    => ''
		]);
	}

	public static function fromAssignment(Assignment $assignment): self {
		$data         = json_decode($assignment->getNewbie(), true);
		$newbie       = new self($data);
		$newbie->uuid = (string)$assignment->getUuid();
		$newbie->setUser($assignment->getUser());
		return $newbie;
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

	public function getWood(): int {
		return (int)$this->properties['holz'];
	}

	public function getStone(): int {
		return (int)$this->properties['steine'];
	}

	public function getIron(): int {
		return (int)$this->properties['eisen'];
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

	public function toLemuriaJson(): string {
		return json_encode(
			['name' => $this->getName(), 'description' => $this->getDescription(), 'rasse' => $this->getRace()]
		);
	}
}

<?php
declare (strict_types = 1);
namespace App\Game;

use App\Data\Newbie as NewbieData;
use App\Entity\User;

class Newbie
{
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

	public function __construct(private array $properties) {
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

	public function setUser(User $user): self {
		$this->properties['email']   = $user->getEmail();
		$this->properties['user_id'] = $user->getId();
		return $this;
	}
}

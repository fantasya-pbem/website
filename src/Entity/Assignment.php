<?php
declare(strict_types = 1);
namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

use App\Repository\AssignmentRepository;

#[Entity(repositoryClass: AssignmentRepository::class)]
class Assignment
{
	#[Column(length: 36)]
	#[Id]
	private string $uuid = '';

	#[ManyToOne(targetEntity: User::class)]
	private ?User $user = null;

	#[Column]
	private bool $retired = false;

	public function getUuid(): ?string {
		return $this->uuid;
	}

	public function setUuid(string $uuid): self {
		$this->uuid = $uuid;
		return $this;
	}

	public function getUser(): ?User {
		return $this->user;
	}

	public function setUser(User $user): self {
		$this->user = $user;
		return $this;
	}

	public function hasRetired(): bool {
		return $this->retired;
	}

	public function retire(): self {
		$this->retired = true;
		return $this;
	}
}

<?php
declare(strict_types = 1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\AssignmentRepository;

/**
 * @ORM\Entity(repositoryClass=AssignmentRepository::class)
 */
class Assignment
{
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="string", length=36)
	 */
	private string $uuid = '';

	/**
	 * @ORM\ManyToOne(targetEntity=User::class)
	 * @ORM\JoinColumn(nullable=false)
	 */
	private User $user;

	/**
	 * @ORM\Column(type="string")
	 */
	private ?string $newbie = null;

	public function getUuid(): ?string {
		return $this->uuid;
	}

	public function setUuid(string $uuid): self {
		$this->uuid = $uuid;
		return $this;
	}

	public function getUser(): User {
		return $this->user;
	}

	public function setUser(User $user): self {
		$this->user = $user;
		return $this;
	}

	public function getNewbie(): ?string {
		return $this->newbie;
	}

	public function setNewbie(string $newbie): self {
		$this->newbie = $newbie;
		return $this;
	}
}

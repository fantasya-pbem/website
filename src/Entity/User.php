<?php
declare(strict_types = 1);
namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Data\Flags;
use App\Data\PasswordReset;
use App\Repository\UserRepository;
use App\Security\Role;

#[Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[Column]
	#[GeneratedValue]
	#[Id]
	private ?int $id = null;

	#[Column(length: 190, unique: true)]
	private string $name = '';

	/**
	 * @var array<string>
	 */
	#[Column(type: 'json')]
	private array $roles = [];

	#[Column(type: 'smallint')]
	private int $flags = 0;

	#[Column]
	private string $password = '';

	#[Column(length: 190, unique: true)]
	private string $email = '';

	/**
	 * @return array<string>
	 */
	public function getRoles(): array {
		$roles = $this->roles;
		// guarantee every user at least has Role::USER
		$roles[] = Role::USER;
		return array_unique($roles);
	}

	public function eraseCredentials(): void {
	}

	public function getUserIdentifier(): string {
		return $this->name;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function getUsername(): string {
		return $this->name;
	}

	public function hasRole(string $role): bool {
		return in_array($role, $this->getRoles());
	}

	/**
	 * @param array<string> $roles
	 */
	public function setRoles(array $roles): self {
		$this->roles = $roles;
		return $this;
	}

	public function setPassword(string $password): self {
		$this->password = $password;
		return $this;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function setEmail(string $email): self {
		$this->email = strtolower($email);
		return $this;
	}

	public function getFlags(): Flags {
		return new Flags($this->flags);
	}

	public function setFlags(Flags $flags): self {
		$this->flags = $flags->get();
		return $this;
	}

	public function from(PasswordReset $resetOrRegistration): self {
		return $this
			->setName($resetOrRegistration->getName())
			->setPassword('')
			->setEmail($resetOrRegistration->getEmail())
			->setRoles([Role::USER]);
	}
}

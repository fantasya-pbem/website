<?php
declare(strict_types = 1);
namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Data\PasswordReset;
use App\Repository\UserRepository;
use App\Security\Role;

#[Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	public final const FLAG_WITH_ATTACHMENT = 1;

	private const ALL_FLAGS = [self::FLAG_WITH_ATTACHMENT];

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

	/**
	 * @throws \InvalidArgumentException
	 */
	public function hasFlag(int $flag): bool {
		$this->validateFlag($flag);
		return ($this->flags & $flag) === 1;
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function setFlag(int $flag, bool $set = true): self {
		$this->validateFlag($flag);
		if ($set) {
			$this->flags |= $flag;
		} else {
			$this->flags &= array_sum(self::ALL_FLAGS) - $flag;
		}
		return $this;
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function validateFlag(int $flag): void {
		if (!in_array($flag, self::ALL_FLAGS)) {
			throw new \InvalidArgumentException('Invalid flag: ' . $flag);
		}
	}

	public function from(PasswordReset $resetOrRegistration): self {
		return $this
			->setName($resetOrRegistration->getName())
			->setPassword('')
			->setEmail($resetOrRegistration->getEmail())
			->setRoles([Role::USER]);
	}

}

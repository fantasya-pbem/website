<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Data\PasswordReset;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
	public const FLAG_WITH_ATTACHMENT = 1;

	public const ROLE_ADMIN = 'ROLE_ADMIN';

	public const ROLE_BETA_TESTER = 'ROLE_BETA_TESTER';

	public const ROLE_MULTI_PLAYER = 'ROLE_MULTI_PLAYER';

	public const ROLE_NEWS_CREATOR = 'ROLE_NEWS_CREATOR';

	public const ROLE_USER = 'ROLE_USER';

	private const ALL_FLAGS = [self::FLAG_WITH_ATTACHMENT];

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private ?int $id = null;

	/**
	 * @ORM\Column(type="string", length=190, unique=true)
	 */
	private string $name = '';

	/**
	 * @ORM\Column(type="json")
	 * @var string[]
	 */
	private array $roles = [];

	/**
	 * @ORM\Column(type="smallint")
	 */
	private int $flags = 0;

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private string $password = '';

	/**
	 * @ORM\Column(type="string", length=190, unique=true)
	 */
	private string $email = '';

	public function getUserIdentifier(): string {
		return 'username';
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
	 * @return string[]
	 */
	public function getRoles(): array {
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = self::ROLE_USER;
		return array_unique($roles);
	}

	/**
	 * @param string[] $roles
	 */
	public function setRoles(array $roles): self {
		$this->roles = $roles;
		return $this;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function setPassword(string $password): self {
		$this->password = $password;
		return $this;
	}

	public function getSalt(): ?string {
		// not needed when using the "bcrypt" algorithm in security.yaml
		return null;
	}

	public function eraseCredentials(): void {
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function setEmail(string $email): self {
		$this->email = strtolower($email);
		return $this;
	}

	public function from(PasswordReset $resetOrRegistration): self {
		return $this
			->setName($resetOrRegistration->getName())
			->setPassword('')
			->setEmail($resetOrRegistration->getEmail())
			->setRoles([User::ROLE_USER]);
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

	public function __call(string $name, array $arguments) {
		// TODO: Implement @method string getUserIdentifier()
	}
}

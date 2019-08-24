<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Data\PasswordReset;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface {

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
	 *
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=190, unique=true)
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="json")
	 *
	 * @var string[]
	 */
	private $roles = [];

	/**
	 * @ORM\Column(type="smallint")
	 *
	 * @var int
	 */
	private $flags;

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 *
	 * @var string
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=190, unique=true)
	 *
	 * @var string
	 */
	private $email;

	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return User
	 */
	public function setName(string $name): self {
		$this->name = $name;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @return string
	 */
	public function getUsername(): string {
		return (string)$this->name;
	}

	/**
	 * @param string $role
	 * @return bool
	 */
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
	 * @param array(string) $roles
	 * @return User
	 */
	public function setRoles(array $roles): self {
		$this->roles = $roles;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string {
		return (string)$this->password;
	}

	/**
	 * @param string $password
	 * @return User
	 */
	public function setPassword(string $password): self {
		$this->password = $password;
		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getSalt() {
		// not needed when using the "bcrypt" algorithm in security.yaml
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials() {
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string {
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return User
	 */
	public function setEmail(string $email): self {
		$this->email = (strtolower($email));
		return $this;
	}

	/**
	 * @param PasswordReset $resetOrRegistration
	 * @return User
	 */
	public function from(PasswordReset $resetOrRegistration): self {
		return $this
			->setName($resetOrRegistration->getName())
			->setPassword('')
			->setEmail($resetOrRegistration->getEmail())
			->setRoles([User::ROLE_USER]);
	}

	/**
	 * @param int $flag
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function hasFlag(int $flag): bool {
		$this->validateFlag($flag);
		return ($this->flags & $flag) === 1;
	}

	/**
	 * @param int $flag
	 * @param bool $set
	 * @return User
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
	 * @param int $flag
	 * @throws \InvalidArgumentException
	 */
	private function validateFlag(int $flag): void {
		if (!in_array($flag, self::ALL_FLAGS)) {
			throw new \InvalidArgumentException('Invalid flag: ' . $flag);
		}
	}
}

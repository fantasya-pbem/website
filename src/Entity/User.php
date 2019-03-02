<?php
declare (strict_types = 1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Data\PasswordReset;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
	const ROLE_ADMIN = 'ROLE_ADMIN';

	const ROLE_BETA_TESTER = 'ROLE_BETA_TESTER';

	const ROLE_MULTI_PLAYER = 'ROLE_MULTI_PLAYER';

	const ROLE_NEWS_CREATOR = 'ROLE_NEWS_CREATOR';

	const ROLE_USER = 'ROLE_USER';

	/**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $email;

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string|null
	 */
    public function getName(): ?string
    {
        return $this->name;
    }

	/**
	 * @param string $name
	 * @return User
	 */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->name;
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
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;
        return array_unique($roles);
    }

	/**
	 * @param array(string) $roles
	 * @return User
	 */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

	/**
	 * @param string $password
	 * @return User
	 */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

	/**
	 * @return string|null
	 */
    public function getEmail(): ?string
    {
        return $this->email;
    }

	/**
	 * @param string $email
	 * @return User
	 */
    public function setEmail(string $email): self
    {
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
}

<?php
declare (strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

use App\Entity\User;

/**
 * @method find($id, ?int $lockMode = null, ?int $lockVersion = null): ?User
 * @method findOneBy(array $criteria, ?array $orderBy = null): ?User
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
		if ($user instanceof User) {
			$user->setPassword($newHashedPassword);
			$this->getEntityManager()->flush($user);
		}
	}

	public function loadUserByIdentifier(string $identifier): ?User {
    	if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
    		return $this->findOneBy(['email' => $identifier]);
		}
    	return $this->findOneBy(['name' => $identifier]);
	}

	public function loadUserByUsername(string $username): User {
		return $this->loadUserByIdentifier($username);
	}

	/**
	 * @throws NonUniqueResultException
	 */
    public function findDuplicate(User $user): ?User {
    	$q = $this->createQueryBuilder('u');
		$q->andWhere($q->expr()->eq('u.name', ':name'));
		$q->orWhere($q->expr()->eq('u.email', ':email'));
		$q->setParameter('name', $user->getName());
		$q->setParameter('email', $user->getEmail());
		return $q->getQuery()->getOneOrNullResult();
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function findExisting(User $user): ?User {
		$q = $this->createQueryBuilder('u');
		$q->andWhere($q->expr()->eq('u.name', ':name'));
		$q->andWhere($q->expr()->eq('u.email', ':email'));
		$q->setParameter('name', $user->getName());
		$q->setParameter('email', $user->getEmail());
		return $q->getQuery()->getOneOrNullResult();
	}
}

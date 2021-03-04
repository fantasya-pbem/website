<?php
declare (strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\User;

/**
 * @method find($id, ?int $lockMode = null, ?int $lockVersion = null): ?User
 * @method findOneBy(array $criteria, ?array $orderBy = null): ?User
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

	/**
	 * @throws ORMException
	 */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void {
		if ($user instanceof User) {
			$user->setPassword($newEncodedPassword);
			$this->getEntityManager()->flush($user);
		}
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

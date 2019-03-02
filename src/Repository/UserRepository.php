<?php
declare (strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\User;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
	/**
	 * @param RegistryInterface $registry
	 */
    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, User::class);
    }

	/**
	 * @param User $user
	 * @return User|null
	 * @throws NonUniqueResultException
	 */
    public function findDuplicate(User $user) {
    	$q = $this->createQueryBuilder('u');
		$q->andWhere($q->expr()->eq('u.name', ':name'));
		$q->orWhere($q->expr()->eq('u.email', ':email'));
		$q->setParameter('name', $user->getName());
		$q->setParameter('email', $user->getEmail());
		return $q->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param User $user
	 * @return User|null
	 * @throws NonUniqueResultException
	 */
	public function findExisting(User $user) {
		$q = $this->createQueryBuilder('u');
		$q->andWhere($q->expr()->eq('u.name', ':name'));
		$q->andWhere($q->expr()->eq('u.email', ':email'));
		$q->setParameter('name', $user->getName());
		$q->setParameter('email', $user->getEmail());
		return $q->getQuery()->getOneOrNullResult();
	}
}

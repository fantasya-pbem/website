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
	 */
	public function findExisting(User $user) {
		$q = $this->createQueryBuilder('u');
		$q->andWhere($q->expr()->eq('u.name', ':name'));
		$q->andWhere($q->expr()->eq('u.email', ':email'));
		$q->setParameter('name', $user->getName());
		$q->setParameter('email', $user->getEmail());
		return $q->getQuery()->getOneOrNullResult();
	}

	// /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

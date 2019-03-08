<?php
declare (strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Myth;

/**
 * @method Myth|null find($id, $lockMode = null, $lockVersion = null)
 * @method Myth|null findOneBy(array $criteria, array $orderBy = null)
 * @method Myth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MythRepository extends ServiceEntityRepository
{
	/**
	 * @param RegistryInterface $registry
	 */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Myth::class);
    }

	/**
	 * @return Myth[]
	 */
    public function findAll(): array {
		 $q = $this->createQuery();
		 return $q->getQuery()->getArrayResult();
	}

	/**
	 * Get the latest myth.
	 *
	 * @return Myth|null
	 * @throws NonUniqueResultException
	 */
    public function getLatest(): ?Myth {
		$q = $this->createQuery()->setMaxResults(1);
    	return $q->getQuery()->getOneOrNullResult();
	}

	/**
	 * @return QueryBuilder
	 */
	private function createQuery(): QueryBuilder {
		$q = $this->createQueryBuilder('m');
		$q->orderBy('m.id', 'DESC');
		return $q;
	}
}

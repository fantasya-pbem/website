<?php
declare (strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Myth;

/**
 * @method Myth|null find($id, $lockMode = null, $lockVersion = null)
 * @method Myth|null findOneBy(array $criteria, array $orderBy = null)
 * @method Myth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MythRepository extends ServiceEntityRepository
{
	const PAGE_SIZE = 15;

	/**
	 * @param RegistryInterface $registry
	 */
    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Myth::class);
    }

	/**
	 * @param int $page
	 * @return Paginator
	 */
    public function findAll(int $page = 1): Paginator {
    	$q = $this->createQuery()->getQuery();
		$q->setFirstResult(--$page * self::PAGE_SIZE)->setMaxResults(self::PAGE_SIZE);
		return new Paginator($q);
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

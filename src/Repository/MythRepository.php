<?php
declare(strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Myth;

/**
 * @method Myth|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Myth|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method array<Myth> findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
class MythRepository extends ServiceEntityRepository
{
	public const PAGE_SIZE = 15;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Myth::class);
    }

    public function findAll(int $page = 1): Paginator {
    	$q = $this->createQuery()->getQuery();
		$q->setFirstResult(--$page * self::PAGE_SIZE)->setMaxResults(self::PAGE_SIZE);
		return new Paginator($q);
	}

	/**
	 * @throws NonUniqueResultException
	 */
    public function getLatest(): ?Myth {
		$q = $this->createQuery()->setMaxResults(1);
    	return $q->getQuery()->getOneOrNullResult();
	}

	private function createQuery(): QueryBuilder {
		$q = $this->createQueryBuilder('m');
		$q->orderBy('m.id', 'DESC');
		return $q;
	}
}

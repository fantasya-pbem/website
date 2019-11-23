<?php
declare (strict_types=1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Game;

/**
 * @method ?Game find($id, $lockMode = null, $lockVersion = null)
 * @method ?Game findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
	/**
	 * @param ManagerRegistry $registry
	 */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Game::class);
    }
	/**
	 * @return Game[]
	 */
	public function findAll(): array {
		$q = $this->createQueryBuilder('g');
		$q->andWhere($q->expr()->eq('g.is_active', 1));
		return $q->getQuery()->getResult();
	}
}

<?php
declare(strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Game;

/**
 * @method Game|null findOneBy(array $criteria, ?array $orderBy = null)
 */
class GameRepository extends ServiceEntityRepository
{
   public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Game::class);
    }

	/**
	 * @return array<Game>
	 */
	public function findAll(): array {
		$q = $this->createQueryBuilder('g');
		$q->andWhere($q->expr()->eq('g.is_active', 1));
		$q->orderBy('g.can_enter', 'DESC');
		return $q->getQuery()->getResult();
	}

	public function findByName(string $name): ?Game {
		return $this->findOneBy(['name' => $name]);
	}

	public function findByAlias(string $alias): ?Game {
		return $this->findOneBy(['alias' => $alias]);
	}
}

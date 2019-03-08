<?php
declare (strict_types=1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Game;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
	/**
	 * @return Game[]
	 */
	public function findAll(): array {
		$q = $this->createQueryBuilder('g');
		$q->andWhere($q->expr()->eq('g.is_active', 1));
		return $q->getQuery()->getResult();
	}

	/**
	 * @param RegistryInterface $registry
	 */
    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Game::class);
    }
}

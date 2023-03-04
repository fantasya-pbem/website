<?php
declare(strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Assignment;
use App\Entity\User;

/**
 * @method Assignment find($id, ?int $lockMode = null, ?int $lockVersion = null)
 */
class AssignmentRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Assignment::class);
	}

	/**
	 * @return array<Assignment>
	 */
	public function findRetired(): array {
		$q = $this->createQueryBuilder('a');
		$q->andWhere($q->expr()->eq('a.retired', 1));
		return $q->getQuery()->getResult();
	}

	/**
	 * @return array<Assignment>
	 */
	public function findByUser(User $user): array {
		return parent::findBy(['user' => $user, 'retired' => false]);
	}

	public function findByUuid(string $uuid): ?Assignment {
		/** @var Assignment $assignment */
		$assignment = parent::findOneBy(['uuid' => $uuid]);
		return $assignment;
	}
}

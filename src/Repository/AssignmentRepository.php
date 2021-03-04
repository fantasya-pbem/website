<?php
declare(strict_types = 1);
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Assignment;
use App\Entity\User;

/**
 * @method find($id, ?int $lockMode = null, ?int $lockVersion = null): ?User
 */
class AssignmentRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Assignment::class);
	}

	/**
	 * @return Assignment[]
	 */
	public function findFor(User $user): array {
		return parent::findBy(['user' => $user]);
	}
}

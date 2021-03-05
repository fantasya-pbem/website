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
		$assignments = [];
		foreach ($this->findByUser($user) as $assignment) {
			if (!str_starts_with($assignment->getUuid(), 'newbie-')) {
				$assignments[] = $assignment;
			}
		}
		return $assignments;
	}

	/**
	 * @return Assignment[]
	 */
	public function findNewbiesFor(User $user): array {
		$newbies = [];
		foreach ($this->findByUser($user) as $assignment) {
			if (str_starts_with($assignment->getUuid(), 'newbie-')) {
				$newbies[] = $assignment;
			}
		}
		return $newbies;
	}

	/**
	 * @return Assignment[]
	 */
	public function findByUser(User $user): array {
		return parent::findBy(['user' => $user]);
	}
}

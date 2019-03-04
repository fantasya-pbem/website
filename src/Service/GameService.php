<?php
declare (strict_types = 1);
namespace App\Service;

use App\Entity\Game;
use App\Repository\GameRepository;

/**
 * A service for templates to fetch myths.
 */
class GameService
{
	/**
	 * @var GameRepository
	 */
	private $repository;

	/**
	 * @param GameRepository $repository
	 */
	public function __construct(GameRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * Get all games.
	 *
	 * @return Game[]
	 */
	public function getAll(): array {
		return $this->repository->findAll();
	}
}

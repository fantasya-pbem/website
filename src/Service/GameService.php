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
	 * @var Game[]
	 */
	private $games;

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
		if ($this->games === null) {
			$this->games = $this->repository->findAll();
		}
		return $this->games;
	}
}

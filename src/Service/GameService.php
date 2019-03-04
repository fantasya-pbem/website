<?php
declare (strict_types = 1);
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Game;
use App\Game\Turn;
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
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * @param GameRepository $repository
	 */
	public function __construct(GameRepository $repository, EntityManagerInterface $manager) {
		$this->repository = $repository;
		$this->manager    = $manager;
	}

	/**
	 * Get all games.
	 *
	 * @return Game[]
	 */
	public function getAll(): array {
		return $this->repository->findAll();
	}

	/**
	 * Get the Turn of a Game.
	 *
	 * @param Game $game
	 * @return Turn
	 */
	public function getTurn(Game $game): Turn {
		return new Turn($game, $this->getRound($game), $this->getLastTurn($game));
	}

	/**
	 * @param Game $game
	 * @return int
	 */
	private function getRound(Game $game): int {
		$table = $game->getDb() . '.settings';
		$sql   = "SELECT value FROM " . $table . " WHERE name = 'game.runde'";
		$stmt  = $this->manager->getConnection()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
		return (int)($result[0] ?? 0);
	}

	/**
	 * @param Game $game
	 * @return string
	 */
	private function getLastTurn(Game $game): \DateTime {
		$table = $game->getDb() . '.meldungen';
		$sql   = "SELECT MAX(zeit) FROM " . $table;
		$stmt  = $this->manager->getConnection()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
		return new \DateTime($result[0] ?? 'now');
	}
}

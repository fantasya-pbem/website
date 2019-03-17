<?php
declare (strict_types = 1);
namespace App\Service;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * @var Game[]
	 */
	private $games;

	/**
	 * @param GameRepository $repository
	 * @param SessionInterface $session
	 */
	public function __construct(GameRepository $repository, SessionInterface $session) {
		$this->repository = $repository;
		$this->session    = $session;
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

	/**
	 * Get current Game.
	 *
	 * @return Game
	 */
	public function getCurrent(): Game {
		if ($this->session && $this->session->has('game')) {
			 $game = $this->session->get('game');
			 if ($game instanceof Game) {
			 	return $game;
			 }
		}
		$games = $this->getAll();
		$game  = current($games);
		if ($this->session) {
			$this->session->set('game', $game);
		}
		return $game;
	}
}

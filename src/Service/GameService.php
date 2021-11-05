<?php
declare (strict_types = 1);
namespace App\Service;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GameService
{
	private SessionInterface $session;

	/**
	 * @var Game[]
	 */
	private ?array $games = null;

	public function __construct(private GameRepository $repository, private RequestStack $requestStack) {
		$this->session = $this->requestStack->getSession();
	}

	/**
	 * @return Game[]
	 */
	public function getAll(): array {
		if ($this->games === null) {
			$this->games = $this->repository->findAll();
		}
		return $this->games;
	}

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

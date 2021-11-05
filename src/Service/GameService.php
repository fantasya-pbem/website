<?php
declare (strict_types = 1);
namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Game;
use App\Repository\GameRepository;

class GameService
{
	private ?SessionInterface $session = null;

	/**
	 * @var Game[]
	 */
	private ?array $games = null;

	public function __construct(private GameRepository $repository, private RequestStack $requestStack) {
		try {
			$this->session = $this->requestStack->getSession();
		} catch (SessionNotFoundException) {
		}
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

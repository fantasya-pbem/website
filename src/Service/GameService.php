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
	/**
	 * @var array<Game>
	 */
	private ?array $games = null;

	public function __construct(private readonly GameRepository $repository, private readonly RequestStack $requestStack) {
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
		$session = $this->getSession();
		if ($session && $session->has('game')) {
			 $game = $session->get('game');
			 if ($game instanceof Game) {
			 	return $game;
			 }
		}
		$games = $this->getAll();
		$game  = current($games);
		$session?->set('game', $game);
		return $game;
	}

	protected function getSession(): ?SessionInterface {
		try {
			return $this->requestStack->getSession();
		} catch (SessionNotFoundException) {
			return null;
		}
	}
}

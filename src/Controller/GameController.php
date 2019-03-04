<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Game;
use App\Entity\User;
use App\Service\GameService;
use App\Service\PartyService;

/**
 * @IsGranted("ROLE_USER")
 */
class GameController extends AbstractController
{
	/**
	 * @var GameService
	 */
	private $gameService;

	/**
	 * @var PartyService
	 */
	private $partyService;

	/**
	 * @param GameService $gameService
	 * @param PartyService $partyService
	 */
	public function __construct(GameService $gameService, PartyService $partyService) {
		$this->gameService  = $gameService;
		$this->partyService = $partyService;
	}

	/**
	 * @Route("/game/next", name="game_next")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function next(Request $request): Response {
		$games = [];
		foreach ($this->gameService->getAll() as $game) {
			$games[$game->getId()] = $game;
		}
		reset($games);
		/* @var Game $game */
		$game = current($games);

		$session = $request->getSession();
		if ($session && $session->has('game')) {
			/* @var Game $current */
			$current = $session->get('game');
			$id      = $current->getId();
			while (key($games) !== $id) {
				if (!next($games)) {
					break;
				}
			}
			$next = next($games);
			if ($next) {
				$game = $next;
			}
		}
		$session->set('game', $game);

		$parties = $this->partyService->getFor($this->user());
		if (empty($parties[$game->getId()])) {
			return $this->redirectToRoute('profile');
		} else {
			return $this->redirectToRoute('order');
		}
	}

	/**
	 * @Route("/game/enter", name="game_enter")
	 *
	 * @return Response
	 */
	public function enter(): Response {
		return $this->render('game/enter.html.twig');
	}

	/**
	 * @return User
	 */
	private function user(): User {
		return $this->getUser();
	}
}

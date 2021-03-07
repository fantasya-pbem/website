<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Data\Lemurian;
use App\Data\Newbie as NewbieData;
use App\Entity\Game;
use App\Entity\User;
use App\Form\LemurianType;
use App\Form\NewbieType;
use App\Game\Engine;
use App\Game\Newbie;
use App\Service\GameService;
use App\Service\MailService;
use App\Service\PartyService;

/**
 * @IsGranted("ROLE_USER")
 */
class GameController extends AbstractController
{
	public function __construct(private GameService $gameService, private PartyService $partyService,
		                        private MailService $mailService) {
	}

	/**
	 * @Route("/game/next", name="game_next")
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
	 */
	public function enter(Request $request): Response {
		if (!$this->canEnter()) {
			return $this->redirectToRoute('profile');
		}
		return match ($this->gameService->getCurrent()->getEngine()) {
			Engine::FANTASYA => $this->enterFantasya($request),
			Engine::LEMURIA  => $this->enterLemuria($request),
			default          => $this->redirectToRoute('profile')
		};
	}

	/**
	 * @Route("/game/{game}/revoke/{name}", name="game_revoke")
	 */
	public function revoke(Game $game, string $name): Response {
		$user    = $this->user();
		//$game    = $this->gameService->getCurrent();
		$newbies = $this->partyService->getNewbies($this->user());
		$delete  = [];
		foreach ($newbies as $id => $gameNewbies) {
			foreach ($gameNewbies as $newbie /* @var Newbie $newbie */) {
				if ($id === $game->getId()) {
					if ($newbie->getName() === $name && $newbie->getUserId() === $user->getId()) {
						$delete[] = $newbie;
					}
				}
			}
		}
		if (count($delete) === 1) {
			$this->partyService->delete($delete[0], $game);
		}
		return $this->redirectToRoute('profile');
	}

	/**
	 * @return User
	 */
	private function user(): User {
		/** @var User $user */
		$user = $this->getUser();
		return $user;

	}

	private function canEnter(): bool {
		if ($this->isGranted(User::ROLE_MULTI_PLAYER)) {
			return true;
		}
		if (!$this->partyService->hasAny($this->user(), $this->gameService->getCurrent())) {
			return true;
		}
		return false;
	}

	private function enterFantasya(Request $request): Response {
		$newbieData = new NewbieData();
		$resources  = false;
		$form       = $this->createForm(NewbieType::class, $newbieData);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var NewbieData $newbieData */
			$newbieData = $form->getData();
			if ($newbieData->getResources() <= 90) {
				$newbie = Newbie::fromData($newbieData)->setUser($this->user());
				$this->partyService->create($newbie);
				$this->sendAdminMail($newbie);
				return $this->redirectToRoute('profile');
			}
			$resources = true;
		}

		return $this->render('game/enter-fantasya.html.twig', ['form' => $form->createView(), 'resources' => $resources]);
	}

	private function enterLemuria(Request $request): Response {
		$lemurian = new NewbieData();
		$form     = $this->createForm(LemurianType::class, $lemurian);
		try {
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()) {
				/* @var NewbieData $newbieData */
				$newbieData = $form->getData();
				$newbie     = Newbie::fromData($newbieData)->setUser($this->user());
				$this->partyService->create($newbie);
				$this->sendAdminMail($newbie);
				return $this->redirectToRoute('profile');
			}
		} catch (\InvalidArgumentException) {
		}

		return $this->render('game/enter-lemuria.html.twig', ['form' => $form->createView()]);
	}

	private function sendAdminMail(Newbie $newbie) {
		$mail = $this->mailService->toGameMaster();
		$mail->subject('Neue Fantasya-Partei');
		$mail->text($this->renderView('emails/admin_party.html.twig', ['user' => $this->user(), 'newbie' => $newbie]));
		try {
			$this->mailService->signAndSend($mail);
		} catch (\Throwable) {
		}
	}
}

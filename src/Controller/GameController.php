<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Data\Lemurian;
use App\Data\Newbie as NewbieData;
use App\Entity\Game;
use App\Entity\User;
use App\Form\NewbieType;
use App\Game\Engine;
use App\Game\Newbie;
use App\Game\Race;
use App\Security\Role;
use App\Service\GameService;
use App\Service\MailService;
use App\Service\PartyService;

#[IsGranted(Role::USER)]
class GameController extends AbstractController
{
	public function __construct(private readonly GameService $gameService, private readonly PartyService $partyService,
		                        private readonly MailService $mailService) {
	}

	#[Route('/welt', 'game_next')]
	public function next(Request $request): Response {
		$games = [];
		foreach ($this->gameService->getAll() as $game) {
			$games[$game->getId()] = $game;
		}
		/** @var Game $game */
		$game = current($games);

		$session = $request->getSession();
		if ($session->has('game')) {
			/** @var Game $current */
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

		$parties = $this->partyService->getFor($this->user());
		if (empty($parties[$game->getId()])) {
			return $this->redirectToRoute('profile');
		} else {
			$session->set('game', $game);
			return $this->redirectToRoute('order');
		}
	}

	#[Route('/betreten/{race}', 'game_enter')]
	public function enter(Request $request, string $race = ''): Response {
		if (!$this->canEnter()) {
			return $this->redirectToRoute('profile');
		}
		if ($this->gameService->getCurrent()->getEngine() !== Engine::LEMURIA) {
			return $this->redirectToRoute('profile');
		}

		if (empty($race) || !in_array($race, Race::all())) {
			return $this->render('game/enter.html.twig');
		}

		$lemurian = new NewbieData();
		$lemurian->setRace($race);
		$form = $this->createForm(NewbieType::class, $lemurian);
		try {
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()) {
				/** @var NewbieData $newbieData */
				$newbieData = $form->getData();
				$newbie     = Newbie::fromData($newbieData)->setUser($this->user());
				$this->partyService->create($newbie);
				$this->sendAdminMail($newbie);
				return $this->redirectToRoute('profile');
			}
		} catch (\InvalidArgumentException) {
		}

		return $this->render('game/enter.html.twig', ['form' => $form->createView()]);
	}

	#[Route('/verlassen/{game}/{name}', 'game_revoke')]
	public function revoke(Game $game, string $name): Response {
		$user    = $this->user();
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

	private function user(): User {
		/** @var User $user */
		$user = $this->getUser();
		return $user;

	}

	private function canEnter(): bool {
		$game = $this->gameService->getCurrent();
		if (!$game->getCanEnter()) {
			return false;
		}
		if ($this->isGranted(Role::MULTI_PLAYER)) {
			return true;
		}
		if (!$this->partyService->hasAny($this->user(), $game)) {
			return true;
		}
		return false;
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

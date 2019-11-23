<?php
declare (strict_types = 1);
namespace App\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

use App\Data\Newbie as NewbieData;
use App\Entity\Game;
use App\Entity\User;
use App\Form\NewbieType;
use App\Game\Newbie;
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
	 * @var MailerInterface
	 */
	private $mailer;

	/**
	 * @param GameService $gameService
	 * @param PartyService $partyService
	 * @param MailerInterface $mailer
	 */
	public function __construct(GameService $gameService, PartyService $partyService, MailerInterface $mailer) {
		$this->gameService  = $gameService;
		$this->partyService = $partyService;
		$this->mailer       = $mailer;
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
	 * @param Request $request
	 * @return Response
	 */
	public function enter(Request $request): Response {
		if (!$this->canEnter()) {
			return $this->redirectToRoute('profile');
		}

		$newbieData= new NewbieData();
		$resources = false;
		$form      = $this->createForm(NewbieType::class, $newbieData);
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

		return $this->render('game/enter.html.twig', [
			'form'      => $form->createView(),
			'resources' => $resources
		]);
	}

	/**
	 * @Route("/game/revoke/{name}", name="game_revoke")
	 *
	 * @param string $name
	 * @return Response
	 * @throws DBALException
	 */
	public function revoke(string $name): Response {
		$user    = $this->user();
		$game    = $this->gameService->getCurrent();
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
			$this->partyService->delete($delete[0]);
		}
		return $this->redirectToRoute('profile');
	}

	/**
	 * @return User
	 */
	private function user(): User {
		return $this->getUser();
	}

	/**
	 * @return bool
	 * @throws DBALException
	 */
	private function canEnter(): bool {
		if ($this->isGranted(User::ROLE_MULTI_PLAYER)) {
			return true;
		}
		if (!$this->partyService->hasAny($this->user(), $this->gameService->getCurrent())) {
			return true;
		}
		return false;
	}

	/**
	 * @param Newbie $newbie
	 */
	private function sendAdminMail(Newbie $newbie) {
		$mail = new Email();
		$mail->from(new Address($this->getParameter('app.mail.admin.address'), $this->getParameter('app.mail.admin.name')));
		$mail->to(new Address($this->getParameter('app.mail.game.address'), $this->getParameter('app.mail.game.name')));
		$mail->subject('Neue Fantasya-Partei');
		$mail->text($this->renderView('emails/admin_party.html.twig', ['user' => $this->user(), 'newbie' => $newbie]));
		try {
			$this->mailer->send($mail);
		} catch (\Throwable $e) {
		}
	}
}

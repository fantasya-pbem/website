<?php
declare (strict_types = 1);
namespace App\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Data\Report;
use App\Entity\Game;
use App\Entity\User;
use App\Game\Party;
use App\Game\Turn;
use App\Security\Token;
use App\Service\GameService;
use App\Service\PartyService;
use App\Service\ReportService;

/**
 *
 */
class ReportController extends AbstractController
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
	 * @var ReportService
	 */
	private $reportService;

	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * @param GameService $gameService
	 * @param PartyService $partyService
	 * @param ReportService $reportService
	 * @param EntityManagerInterface $manager
	 */
	public function __construct(GameService $gameService, PartyService $partyService, ReportService $reportService,
		                        EntityManagerInterface $manager) {
		$this->gameService   = $gameService;
		$this->partyService  = $partyService;
		$this->reportService = $reportService;
		$this->manager       = $manager;
	}

	/**
	 * @Route("/report", name="report")
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws DBALException
	 */
	public function index(Request $request): Response {
		$parties = $this->partyService->getCurrent($this->user());
		if (empty($parties)) {
			return $this->redirectToRoute('profile');
		}

		$forms = [];
		foreach ($parties as $party) {
			$id     = $party->getId();
			$report = new Report();
			$report->setParty($id);
			$report->setGame($this->gameService->getCurrent()->getAlias());
			$this->reportService->setContext($report);
			$form       = $this->createReportForm($report);
			$forms[$id] = $form->createView();
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				/* @var Report $report */
				$report = $form->getData();
				$report->setGame($this->gameService->getCurrent()->getAlias());
				$this->reportService->setContext($report);
				return $this->file($this->reportService->getPath());
			}
		}

		return $this->render('report/index.html.twig', [
			'parties' => $parties,
			'forms'   => $forms
		]);
	}

	/**
	 * @Route("/report/t/{token}", name="report_download", requirements={"token"="[0-9a-f]{23,24}"}))
	 *
	 * @param string $token
	 * @return Response
	 */
	public function download(string $token): Response {
		$tokenPart = substr($token, 0, Token::LENGTH);
		$idPart    = substr($token, Token::LENGTH);
		$gameAndId = hexdec($idPart);
		$gameId    = $gameAndId >> 24;
		$partyId   = Party::toId($gameAndId % 2 ** 24);

		$game = $this->getGame($gameId);
		if ($game) {
			try {
				$turn         = new Turn($game, $this->manager->getConnection());
				$party        = $this->partyService->getById($partyId, $game);
				$token        = new Token();
				$currentToken = (string)$token->setEmail($party->getEmail())->setTurn($turn->getRound());
				if ($tokenPart === $currentToken) {
					$report = new Report();
					$report->setGame($game->getAlias());
					$report->setParty($party->getId());
					$report->setTurn($turn->getRound());
					$this->reportService->setContext($report);
					return $this->file($this->reportService->getPath());
				}
			} catch (DBALException $e) {
			}
		}
		return $this->redirectToRoute('report');
	}

	/**
	 * @return User
	 */
	private function user(): User {
		return $this->getUser();
	}

	/**
	 * @param Report $report
	 * @return FormInterface
	 */
	private function createReportForm(Report $report): FormInterface {
		$turns = $this->reportService->getTurns();
		$turn  = null;
		if (!empty($turns)) {
			$rounds = array_values($turns);
			$turn   = $rounds[count($rounds) - 1];
		}

		$form = $this->createFormBuilder($report);
		$form->add('turn', ChoiceType::class, [
			'label'   => 'Runde',
			'choices' => $turns,
			'data'    => $turn
		]);
		$form->add('submit', SubmitType::class, [
			'label' => 'Herunterladen'
		]);
		return $form->getForm();
	}

	/**
	 * @param int $id
	 * @return Game|null
	 */
	private function getGame(int $id): ?Game {
		foreach ($this->gameService->getAll() as $game) {
			if ($game->getId() === $id) {
				return $game;
			}
		}
		return null;
	}
}

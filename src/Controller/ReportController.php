<?php
declare (strict_types = 1);
namespace App\Controller;

use App\Data\Report;
use App\Entity\User;
use App\Game\Turn;
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

use App\Service\GameService;
use App\Service\PartyService;
use App\Service\ReportService;

/**
 * @IsGranted("ROLE_USER")
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
			$form       = $this->createReportForm($report);
			$forms[$id] = $form->createView();
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				/* @var Report $report */
				$report = $form->getData();
				$report->setGame($this->gameService->getCurrent()->getAlias());
				$this->reportService->setContext($report);
				return $this->reportService->getZip();
			}
		}

		return $this->render('report/index.html.twig', [
			'parties' => $parties,
			'forms'   => $forms
		]);
	}

	/**
	 * @return User
	 */
	private function user(): User {
		return $this->getUser();
	}


	/**
	 * @param Request $request
	 * @return int
	 * @throws DBALException
	 */
	private function turn(Request $request): int {
		$turn  = new Turn($this->gameService->getCurrent(), $this->manager->getConnection());
		$round = $turn->getRound();
		if ($request->request->has('form')) {
			$form = $request->request->get('form');
			if (isset($form['turn'])) {
				$round = (int)$form['turn'];
			}
		}
		return $round;
	}

	/**
	 * @param Report $report
	 * @return FormInterface
	 */
	private function createReportForm(Report $report): FormInterface {
		$form = $this->createFormBuilder($report);
		$form->add('turn', ChoiceType::class, [
			'label'   => 'Runde',
			'choices' => [],
			'data'    => (string)123
		]);
		$form->add('submit', SubmitType::class, [
			'label' => 'Herunterladen'
		]);
		return $form->getForm();
	}
}

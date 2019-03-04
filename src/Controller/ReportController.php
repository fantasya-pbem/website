<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\GameService;
use App\Service\PartyService;

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
	 * @param GameService $gameService
	 * @param PartyService $partyService
	 */
	public function __construct(GameService $gameService, PartyService $partyService) {
		$this->gameService  = $gameService;
		$this->partyService = $partyService;
	}

	/**
	 * @Route("/report", name="report")
	 *
	 * @return Response
	 */
	public function index(): Response {
		return $this->render('report/index.html.twig');
	}
}

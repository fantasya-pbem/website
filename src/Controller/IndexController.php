<?php
declare (strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\GameService;

/**
 * IndexController.
 */
class IndexController extends AbstractController
{
	/**
	 * @var GameRepository
	 */
	private $repository;

	/**
	 * @var GameService
	 */
	private $service;

	/**
	 * @param
	 */
	public function __construct(GameRepository $repository, GameService $service) {
		$this->repository = $repository;
		$this->service    = $service;
	}

	/**
	 * @Route("/", name="index")
	 *
	 * @return Response
	 */
	public function index(): Response {
		return $this->redirectToRoute('news');
	}

	/**
	 * @Route("/about-fantasya", name="about-fantasya")
	 *
	 * @return Response
	 */
	public function about(): Response {
		return $this->render('index/about-fantasya.html.twig');
	}

	/**
	 * @Route("/contact", name="contact")
	 *
	 * @return Response
	 */
	public function contact(): Response {
		return $this->render('index/contact.html.twig');
	}

	/**
	 * @Route("/donate", name="donate")
	 *
	 * @return Response
	 */
	public function donate(): Response {
		return $this->render('index/donate.html.twig');
	}

	/**
	 * @Route("/world/{game}", name="world")
	 *
	 * @param Game $game
	 * @return Response
	 */
	public function world(Game $game): Response {
		$turn = $this->service->getTurn($game);
		return $this->render('index/world.html.twig', [
			'game' => $game,
			'turn' => $turn
		]);
	}
}

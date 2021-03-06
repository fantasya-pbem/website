<?php
declare (strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Game;
use App\Game\Turn;
use App\Service\EngineService;

class IndexController extends AbstractController
{
	public function __construct(private EngineService $engineService, private EntityManagerInterface $manager) {
		\Locale::setDefault('de_DE.utf8');
	}

	/**
	 * @Route("/", name="index")
	 */
	public function index(): Response {
		return $this->redirectToRoute('news');
	}

	/**
	 * @Route("/about-fantasya", name="about-fantasya")
	 */
	public function about(): Response {
		return $this->render('index/about-fantasya.html.twig');
	}

	/**
	 * @Route("/contact", name="contact")
	 */
	public function contact(): Response {
		return $this->render('index/contact.html.twig');
	}

	/**
	 * @Route("/donate", name="donate")
	 */
	public function donate(): Response {
		return $this->render('index/donate.html.twig');
	}

	/**
	 * @Route("/world/{game}", name="world")
	 * @throws \Exception
	 */
	public function world(Game $game): Response {
		$turn       = new Turn($game, $this->engineService);
		$statistics = $this->engineService->get($game)->getStatistics($game);
		return $this->render('index/world.html.twig', [
			'game'       => $game,
			'turn'       => $turn,
			'statistics' => $statistics
		]);
	}
}

<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Game\Turn;
use App\Repository\GameRepository;
use App\Service\EngineService;

class IndexController extends AbstractController
{
	public function __construct(private readonly GameRepository $gameRepository, private readonly EngineService $engineService) {
	}

	#[Route('/', 'index')]
	public function index(): Response {
		return $this->redirectToRoute('news');
	}

	#[Route('/ueber-fantasya', 'about-fantasya')]
	public function about(): Response {
		return $this->render('index/about-fantasya.html.twig');
	}

	#[Route('/kontakt', 'contact')]
	public function contact(): Response {
		return $this->render('index/contact.html.twig');
	}

	#[Route('/spenden', 'donate')]
	public function donate(): Response {
		return $this->render('index/donate.html.twig');
	}

	/**
	 * @throws \Exception
	 */
	#[Route('/welt/{alias}', 'world')]
	public function world(string $alias): Response {
		$id = (int)$alias;
		if ((string)$id === $alias) {
			$game = $this->gameRepository->find($id);
		} else {
			$game = $this->gameRepository->findByAlias($alias);
			if (!$game) {
				$game = $this->gameRepository->findByName($alias);
			}
		}
		if (!$game?->getIsActive()) {
			return $this->redirectToRoute('index');
		}

		$turn       = new Turn($game, $this->engineService);
		$engine     = $this->engineService->get($game);
		$statistics = $engine->getStatistics($game);
		$version    = $engine->getVersion();
		return $this->render('index/world.html.twig', [
			'game'       => $game,
			'turn'       => $turn,
			'statistics' => $statistics,
			'version'    => $version
		]);
	}
}

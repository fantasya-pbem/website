<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Game\Turn;
use App\Repository\GameRepository;
use App\Repository\NewsRepository;
use App\Service\EngineService;

class IndexController extends AbstractController
{
	public function __construct(private readonly NewsRepository $newsRepository, private readonly GameRepository $gameRepository,
		                        private readonly EngineService $engineService) {
	}

	#[Route('/', 'index')]
	public function index(): Response {
		$news = $this->newsRepository->findLatest();
		$game = $this->gameRepository->findByAlias('lemuria');
		$turn = new Turn($game, $this->engineService);

		return $this->render('index/index.html.twig', [
			'news' => $news,
			'game' => $game,
			'turn' => $turn
		]);
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

	#[Route('/sitemap.xml', 'sitemap')]
	public function sitemap(): Response {
		$response = $this->render('index/sitemap.xml.twig');
		$response->headers->set('Content-Type', 'application/xml');
		return $response;
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

		$robots     = $game->getCanEnter() ? 'index, follow' : 'noindex, nofollow';
		$turn       = new Turn($game, $this->engineService);
		$engine     = $this->engineService->get($game);
		$statistics = $engine->getStatistics($game);
		$version    = $engine->getVersion();
		return $this->render('index/world.html.twig', [
			'robots'     => $robots,
			'game'       => $game,
			'turn'       => $turn,
			'statistics' => $statistics,
			'version'    => $version
		]);
	}
}

<?php
declare (strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\NewsRepository;

/**
 * NewsController.
 */
class NewsController extends AbstractController
{
	/**
	 * @var NewsRepository
	 */
	private $repository;

	/**
	 * @param NewsRepository $repository
	 */
	public function __construct(NewsRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * @Route("/news", name="news")
	 *
	 * @return Response
	 */
	public function index(): Response {
		$news = $this->repository->findAll();
		return $this->render('news/index.html.twig', ['news' => $news]);
	}
}

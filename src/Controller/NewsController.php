<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\News;
use App\Form\NewsType;
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
		\Locale::setDefault('de_DE.utf8');
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

	/**
	 * @Route("/news/edit/{article}", name="news_edit")
	 * @IsGranted("ROLE_NEWS_CREATOR")
	 *
	 * @param News|null $article
	 * @return Response
	 */
	public function edit(News $article = null): Response {
		$news       = $this->repository->findAll();
		$parameters = [];
		if ($article) {
			$parameters['article'] = $article->getId();
		} else {
			$article = new News();
		}

		$form = $this->createForm(NewsType::class, $article, [
			'action' => $this->generateUrl('news_create', $parameters)
		])->createView();

		return $this->render('news/edit.html.twig', ['news' => $news, 'form' => $form]);
	}

	/**
	 * @Route("/news/create/{article}", name="news_create")
	 * @IsGranted("ROLE_NEWS_CREATOR")
	 *
	 * @param Request $request
	 * @param News $article
	 * @return Response
	 * @throws \Exception
	 */
	public function create(Request $request, News $article = null): Response {
		if ($article) {
			$date = $article->getCreatedAt();
		} else {
			$article = new News();
			$date    = new \DateTime();
		}
		$form = $this->createForm(NewsType::class, $article);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var News $news */
			$article = $form->getData();
			$article->setCreatedAt($date);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($article);
			$entityManager->flush();
			return $this->redirectToRoute('news');
		}

		return $this->edit();
	}

	/**
	 * @Route("/news/delete/{article}", name="news_delete")
	 * @IsGranted("ROLE_NEWS_CREATOR")
	 *
	 * @param News $article
	 * @return Response
	 */
	public function delete(News $article): Response {
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($article);
		$entityManager->flush();

		return $this->redirectToRoute('news');
	}
}

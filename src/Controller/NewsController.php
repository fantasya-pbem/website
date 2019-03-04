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
use Symfony\Component\Validator\Constraints\Date;

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

	/**
	 * @Route("/news/edit", name="news_edit")
	 * @IsGranted("ROLE_NEWS_CREATOR")
	 *
	 * @return Response
	 */
	public function edit(): Response {
		$news = $this->repository->findAll();
		$form = $this->createForm(NewsType::class, new News(), [
			'action' => $this->generateUrl('news_create')
		])->createView();

		return $this->render('news/edit.html.twig', ['news' => $news, 'form' => $form]);
	}

	/**
	 * @Route("/news/create", name="news_create")
	 * @IsGranted("ROLE_NEWS_CREATOR")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function create(Request $request): Response {
		$news = new News();
		$form = $this->createForm(NewsType::class, $news);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var News $news */
			$news = $form->getData();
			$news->setCreatedAt(new \DateTime());
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($news);
			$entityManager->flush();
			return $this->redirectToRoute('news');
		}

		return $this->edit();
	}

	/**
	 * @Route("/news/delete/{news}", name="news_delete")
	 * @IsGranted("ROLE_NEWS_CREATOR")
	 *
	 * @param News $news
	 * @return Response
	 */
	public function delete(News $news): Response {
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($news);
		$entityManager->flush();

		return $this->redirectToRoute('news');
	}
}

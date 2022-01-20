<?php
declare (strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;

class NewsController extends AbstractController
{
	private EntityManagerInterface $entityManager;

	public function __construct(private NewsRepository $repository, ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
		\Locale::setDefault('de_DE.utf8');
	}

	/**
	 * @Route("/news", name="news")
	 */
	public function index(): Response {
		$news = $this->repository->findAll();
		return $this->render('news/index.html.twig', ['news' => $news]);
	}

	/**
	 * @Route("/news/edit/{article}", name="news_edit")
	 * @IsGranted("ROLE_NEWS_CREATOR")
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
			/** @var News $article */
			$article = $form->getData();
			$article->setCreatedAt($date);
			$this->entityManager->persist($article);
			$this->entityManager->flush();
			return $this->redirectToRoute('news');
		}

		return $this->edit();
	}

	/**
	 * @Route("/news/delete/{article}", name="news_delete")
	 * @IsGranted("ROLE_NEWS_CREATOR")
	 */
	public function delete(News $article): Response {
		$this->entityManager->remove($article);
		$this->entityManager->flush();

		return $this->redirectToRoute('news');
	}
}

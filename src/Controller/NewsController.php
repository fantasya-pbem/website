<?php
declare(strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Security\Role;

class NewsController extends AbstractController
{
	private EntityManagerInterface $entityManager;

	public function __construct(private readonly NewsRepository $repository, ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
	}

	#[Route('/news', 'news')]
	public function index(): Response {
		$news = $this->repository->findAll();
		return $this->render('news/index.html.twig', ['news' => $news]);
	}

	#[IsGranted(Role::NEWS_CREATOR)]
	#[Route('/news/edit/{article}', 'news_edit')]
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
	 * @throws \Exception
	 */
	#[IsGranted(Role::NEWS_CREATOR)]
	#[Route('/news/create/{article}', 'news_create')]
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

	#[IsGranted(Role::NEWS_CREATOR)]
	#[Route('/news/delete/{article}', 'news_delete')]
	public function delete(News $article): Response {
		$this->entityManager->remove($article);
		$this->entityManager->flush();

		return $this->redirectToRoute('news');
	}
}

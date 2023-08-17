<?php
declare(strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Laminas\Feed\Writer\Feed;
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
	private const DEFAULT_FEED_FORMAT = 'rss';

	private const FEED_FORMAT = ['atom', 'rss'];

	private EntityManagerInterface $entityManager;

	public function __construct(private readonly NewsRepository $repository, ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
	}

	#[Route('/neuigkeiten', 'news')]
	public function index(): Response {
		$news = $this->repository->findAll();
		return $this->render('news/index.html.twig', ['news' => $news]);
	}

	#[IsGranted(Role::NEWS_CREATOR)]
	#[Route('/neuigkeit/bearbeiten/{article}', 'news_edit')]
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
	#[Route('/neuigkeit/erstellen/{article}', 'news_create')]
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
	#[Route('/neuigkeit/{article}/loeschen', 'news_delete')]
	public function delete(News $article): Response {
		$this->entityManager->remove($article);
		$this->entityManager->flush();

		return $this->redirectToRoute('news');
	}

	#[Route('/feed/{format}', 'feed')]
	public function feed(string $format = ''): Response {
		$format = strtolower($format);
		if (!in_array($format, self::FEED_FORMAT)) {
			$format = self::DEFAULT_FEED_FORMAT;
		}
		$index    = $this->generateUrl('index');
		$author   = ['name' => $this->getParameter('feed.author.name'), 'email' => $this->getParameter('feed.author.email'), 'uri' => $index];
		$newsLink = $this->generateUrl('news');
		$feedNews = $this->repository->findForFeed();

		$feed = new Feed;
		$feed->setLanguage('de_DE');
		$feed->setTitle($this->getParameter('feed.title'));
		$feed->setId($this->generateUrl('feed', ['format' => $format]));
		$feed->setDescription($this->getParameter('feed.description'));
		$feed->setLink($index);
		foreach (self::FEED_FORMAT as $type) {
			$feed->setFeedLink($this->generateUrl('feed', ['format' => $type]), $type);
		}
		$feed->addAuthor($author);
		$feed->setDateModified($feedNews[0]?->getCreatedAt()->getTimestamp() ?? time());

		foreach ($feedNews as $news) {
			$entry = $feed->createEntry();
			$entry->setId('news-' . $news->getId());
			$entry->setTitle($news->getTitle());
			$entry->setLink($newsLink);
			$entry->setDateModified($news->getCreatedAt()->getTimestamp());
			$entry->setDateCreated($news->getCreatedAt()->getTimestamp());
			$entry->setDescription($news->getTitle() . ': ' . substr($news->getContent(), 0, 50) . '…');
			$entry->setContent($news->getContent());
			$entry->addAuthor($author);
			$feed->addEntry($entry);
		}

		return new Response($feed->export($format), headers: ['Content-Type' => 'application/' . $format . '+xml; charset=UTF-8']);
	}
}

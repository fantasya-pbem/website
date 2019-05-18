<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Myth;
use App\Form\MythType;
use App\Repository\MythRepository;

/**
 * MythController.
 */
class MythController extends AbstractController
{
	/**
	 * @var MythRepository
	 */
	private $repository;

	/**
	 * @param MythRepository $repository
	 */
	public function __construct(MythRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * @Route("/myth/{page}", name="myth", requirements={"page"="\d+"})
	 *
	 * @param int $page
	 * @return Response
	 */
	public function index(int $page = 1): Response {
		if ($page <= 0) {
			$page = 1;
		}
		$myths = $this->repository->findAll($page);
		$next  = count($myths) > $page * MythRepository::PAGE_SIZE ? $page + 1 : $page;

		return $this->render('myth/index.html.twig', ['myths' => $myths, 'page' => $page, 'next' => $next]);
	}

	/**
	 * @Route("/myth/spread", name="myth_spread")
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function spread(Request $request): Response {
		$myth = new Myth();
		$form = $this->createForm(MythType::class, $myth);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$myth          = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($myth);
			$entityManager->flush();
			return $this->redirectToRoute('myth');
		}

		return $this->render('myth/spread.html.twig', ['form' => $form->createView()]);
	}
}

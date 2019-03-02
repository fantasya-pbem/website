<?php
declare (strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
	 * @Route("/myth", name="myth")
	 *
	 * @return Response
	 */
	public function index(): Response {
		$myths = $this->repository->findAll();
		return $this->render('myth/index.html.twig', ['myths' => $myths]);
	}
}

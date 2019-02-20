<?php
declare (strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * NewsController.
 */
class NewsController extends AbstractController
{
	/**
	 * @Route("/news", name="news")
	 *
	 * @return Response
	 */
	public function index(): Response {
		return $this->render('news/index.html.twig');
	}
}

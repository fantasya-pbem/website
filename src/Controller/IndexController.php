<?php
declare (strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * IndexController.
 */
class IndexController extends AbstractController
{
	/**
	 * @Route("/", name="index")
	 */
	public function index() {
		return $this->redirectToRoute('about');
	}

	/**
	 * @Route("/about", name="about")
	 */
	public function about() {
		return $this->render('index/about.html.twig');
	}

	/**
	 * @Route("/contact", name="contact")
	 */
	public function contact() {
		return $this->render('index/contact.html.twig');
	}

	/**
	 * @Route("/donate", name="donate")
	 */
	public function donate() {
		return $this->render('index/donate.html.twig');
	}
}

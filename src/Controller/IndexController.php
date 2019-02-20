<?php
declare (strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * IndexController.
 */
class IndexController extends AbstractController
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * @param UserPasswordEncoderInterface $encoder
	 */
	public function __construct(UserPasswordEncoderInterface $encoder) {
		$this->encoder = $encoder;
	}

	/**
	 * @Route("/", name="index")
	 *
	 * @return Response
	 */
	public function index(): Response {
		return $this->redirectToRoute('news');
	}

	/**
	 * @Route("/about-fantasya", name="about-fantasya")
	 *
	 * @return Response
	 */
	public function about(): Response {
		return $this->render('index/about-fantasya.html.twig');
	}

	/**
	 * @Route("/contact", name="contact")
	 *
	 * @return Response
	 */
	public function contact(): Response {
		return $this->render('index/contact.html.twig');
	}

	/**
	 * @Route("/donate", name="donate")
	 *
	 * @return Response
	 */
	public function donate(): Response {
		return $this->render('index/donate.html.twig');
	}
}

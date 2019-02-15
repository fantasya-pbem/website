<?php
declare (strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * MythController.
 */
class MythController extends AbstractController
{
	/**
	 * @Route("/myth", name="myth")
	 */
	public function index() {
		return $this->render('myth/index.html.twig');
	}
}

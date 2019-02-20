<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
	/**
	 * @Route("/profile", name="profile")
	 *
	 * @return Response
	 */
	public function profile(): Response {
		return $this->render('profile/index.html.twig');
	}
}

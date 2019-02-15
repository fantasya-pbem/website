<?php
declare (strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ContactController.
 */
class PrivacyController extends AbstractController
{
	/**
	 * @Route("/privacy", name="privacy")
	 */
	public function index() {
		$hasAccepted = isset($_COOKIE['accept_dsgvo']);
		$form        = $this->getForm()->createView();

		return $this->render('privacy/index.html.twig', [
			'hasAccepted' => $hasAccepted,
			'form'        => $form
		]);
	}

	/**
	 * @Route("/privacy/accept", name="privacy_accept")
	 *
	 * @param Request $request
	 */
	public function accept(Request $request) {
		$form = $this->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			return $this->redirectToRoute('index');
		}

		return $this->render('privacy/index.html.twig', [
			'hasAccepted' => false,
			'form'        => $form->createView()
		]);
	}

	/**
	 * @return FormInterface
	 */
	private function getForm(): FormInterface {
		$form = $this->createFormBuilder()->setAction($this->generateUrl('privacy_accept'));
		$form->add('isAccepted', CheckboxType::class);
		$form->add('accept', SubmitType::class);
		return $form->getForm();
	}
}

<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrivacyController extends AbstractController
{
	private const COOKIE = 'accept_dsgvo';

	#[Route('/privacy', 'privacy')]
	public function index(): Response {
		$hasAccepted = isset($_COOKIE[self::COOKIE]);
		$form        = $this->getForm()->createView();

		return $this->render('privacy/index.html.twig', [
			'hasAccepted' => $hasAccepted,
			'form'        => $form
		]);
	}

	#[Route('/privacy/accept', 'privacy_accept')]
	public function accept(Request $request): Response {
		$form = $this->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			setcookie(self::COOKIE, '1', time() + 365 * 24 * 60 * 60, '/');
			return $this->redirectToRoute('news');
		}

		return $this->render('privacy/index.html.twig', [
			'hasAccepted' => false,
			'form'        => $form->createView()
		]);
	}

	private function getForm(): FormInterface {
		$form = $this->createFormBuilder()->setAction($this->generateUrl('privacy_accept'));
		$form->add('isAccepted', CheckboxType::class, [
			'label' => 'Ich möchte an Fantasya teilnehmen und akzeptiere diese Bedingungen.'
		]);
		$form->add('accept', SubmitType::class, ['label' => 'Zustimmen']);
		return $form->getForm();
	}
}

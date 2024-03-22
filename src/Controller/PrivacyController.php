<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use App\Service\PrivacyService;

class PrivacyController extends AbstractController
{
	public final const string COOKIE = 'accept_dsgvo';

	private const string RETURN = 'news';

	public function __construct(private readonly PrivacyService $service) {
	}

	#[Route('/datenschutz/{return}', 'privacy')]
	public function index(Request $request, string $return = ''): Response {
		$form = $this->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			setcookie(self::COOKIE, '1', time() + 365 * 24 * 60 * 60, '/');
			return $this->redirectToRoute($return ?: self::RETURN);
		}

		if ($this->service->hasAcceptedDsgvo() && $return) {
			try {
				return $this->redirectToRoute($return);
			} catch (RouteNotFoundException) {
				return $this->redirectToRoute('privacy');
			}
		}
		return $this->render('privacy/index.html.twig', ['form' => $form->createView()]);
	}

	public function askAction(): RedirectResponse {
		return $this->redirectToRoute('privacy', ['return' => $this->service->getReturn()]);
	}

	private function getForm(): FormInterface {
		$form = $this->createFormBuilder();
		$form->add('isAccepted', CheckboxType::class, [
			'label' => 'Ich möchte an Fantasya teilnehmen und akzeptiere diese Bedingungen.'
		]);
		$form->add('accept', SubmitType::class, ['label' => 'Zustimmen']);
		return $form->getForm();
	}
}

<?php
declare(strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Data\PasswordReset;
use App\Data\Registration;
use App\Entity\User;
use App\Form\PasswordResetType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\PrivacyService;

class UserController extends AbstractController
{
	private EntityManagerInterface $entityManager;

	public function __construct(private readonly UserRepository $repository, private readonly MailService $mailService,
	                            private readonly PrivacyService $userService, private readonly UserPasswordHasherInterface $passwordHasher,
	                            ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
	}

	#[Route('/anmeldung-fehlgeschlagen', 'user_login')]
    public function login(): Response {
        return $this->render('user/login.html.twig');
    }

	#[Route('/anmelden', 'user_secure')]
    public function secure(): Response {
		return $this->redirectToRoute('profile');
	}

	/**
	 * @throws \Throwable
	 */
	#[Route('/registrieren', 'user_register')]
	public function register(Request $request): Response {
		if (!$this->userService->hasAcceptedDsgvo()) {
			return $this->redirectToRoute('privacy', ['return' => 'user_register']);
		}

		$answer       = $this->getParameter('app.antispam.answer');
		$form         = $this->createForm(RegistrationType::class, new Registration($answer));
		$existingUser = null;
		$forumUrl     = $this->getParameter('app.antispam.url');
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user = new User();
			$user->from($form->getData());
			$existingUser = $this->repository->findDuplicate($user);
			if (!$existingUser) {
				$password = uniqid();
				$user->setPassword($this->passwordHasher->hashPassword($user, $password));
				$this->entityManager->persist($user);
				$this->entityManager->flush();
				$this->sendMail($user, $password);
				$this->sendAdminMail($user);
				return $this->redirectToRoute('user_registered');
			}
		}

		return $this->render('user/register.html.twig', [
			'form'     => $form->createView(),
			'existing' => $existingUser,
			'antispam' => $forumUrl
		]);
	}

	#[Route('/registrierung-erfolgreich', 'user_registered')]
	public function registered(): Response {
		return $this->render('user/registered.html.twig');
	}

	/**
	 * @throws \Throwable
	 */
	#[Route('/passwort-vergessen', 'user_reset')]
	public function reset(Request $request): Response {
		if (!$this->userService->hasAcceptedDsgvo()) {
			return $this->redirectToRoute('privacy', ['return' => 'user_reset']);
		}

		$form  = $this->createForm(PasswordResetType::class, new PasswordReset());
		$error = null;
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$resetUser = new User();
			$resetUser->from($form->getData());
			$user = $this->repository->findExisting($resetUser);
			if ($user) {
				$password = uniqid();
				$user->setPassword($this->passwordHasher->hashPassword($user, $password));
				$this->entityManager->persist($user);
				$this->entityManager->flush();
				$this->sendMail($user, $password);
				return $this->redirectToRoute('user_resetted');
			} else {
				$error = true;
			}
		}

		return $this->render('user/reset.html.twig', ['form' => $form->createView(), 'error' => $error]);
	}

	#[Route('/passwort-zurueckgesetzt', 'user_resetted')]
	public function resetted(): Response {
		return $this->render('user/resetted.html.twig');
	}

	#[IsGranted(Role::USER)]
	#[Route('/abmelden', 'user_logout')]
    public function logout(): Response {
    	return $this->redirectToRoute('index');
	}

	/**
	 * @throws \Throwable
	 */
	private function sendMail(User $user, string $password): void {
		$mail = $this->mailService->fromAdmin($user);
		$mail->subject('Fantasya-Registrierung');
		$mail->text($this->renderView('emails/user_reset.html.twig', ['user' => $user, 'password' => $password]));
		$this->mailService->signAndSend($mail);
	}

	private function sendAdminMail(User $user): void {
		$mail = $this->mailService->toGameMaster();
		$mail->subject('Neue Fantasya-Registrierung');
		$mail->text($this->renderView('emails/admin_user.html.twig', ['user' => $user]));
		try {
			$this->mailService->signAndSend($mail);
		} catch (\Throwable) {
		}
	}
}

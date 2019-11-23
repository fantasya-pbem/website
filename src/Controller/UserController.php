<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Data\PasswordReset;
use App\Data\Registration;
use App\Entity\User;
use App\Form\PasswordResetType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
	/**
	 * @var UserRepository
	 */
	private $repository;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;

	/**
	 * @var MailerInterface
	 */
	private $mailer;

	/**
	 * @param UserRepository $repository
	 * @param UserPasswordEncoderInterface $encoder
	 * @param MailerInterface $mailer
	 */
	public function __construct(UserRepository $repository, UserPasswordEncoderInterface $encoder,
								MailerInterface $mailer) {
		$this->repository      = $repository;
		$this->passwordEncoder = $encoder;
		$this->mailer          = $mailer;
	}

	/**
     * @Route("/user/login", name="user_login")
	 *
	 * @return Response
     */
    public function login(): Response {
        return $this->render('user/login.html.twig');
    }

	/**
	 * @Route("/user/secure", name="user_secure")
	 *
	 * @return Response
	 */
    public function secure(): Response {
		return $this->login();
	}

	/**
	 * @Route("/user/expire/{days}", name="user_expire")
	 *
	 * @param int $days
	 * @return Response
	 */
	public function expire(int $days): Response {
    	return $this->render('user/expire.html.twig', [
    		'days' => $days
		]);
	}

	/**
	 * @Route("/user/register", name="user_register")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function register(Request $request): Response {
		$answer       = $this->getParameter('app.antispam.answer');
		$form         = $this->createForm(RegistrationType::class, new Registration($answer));
		$existingUser = null;
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user = new User();
			$user->from($form->getData());
			$existingUser = $this->repository->findDuplicate($user);
			if (!$existingUser) {
				$password = uniqid();
				$user->setPassword($this->passwordEncoder->encodePassword($user, $password));
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($user);
				$entityManager->flush();
				$this->sendMail($user, $password);
				$this->sendAdminMail($user);
				return $this->redirectToRoute('user_registered');
			}
		}

		return $this->render('user/register.html.twig', [
			'form'     => $form->createView(),
			'existing' => $existingUser
		]);
	}

	/**
	 * @Route("/user/registered", name="user_registered")
	 *
	 * @return Response
	 */
	public function registered(): Response {
		return $this->render('user/registered.html.twig');
	}

	/**
	 * @Route("/user/reset", name="user_reset")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function reset(Request $request): Response {
		$form  = $this->createForm(PasswordResetType::class, new PasswordReset());
		$error = null;
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$resetUser = new User();
			$resetUser->from($form->getData());
			$user = $this->repository->findExisting($resetUser);
			if ($user) {
				$password = bin2hex(random_bytes(5));
				$user->setPassword($this->passwordEncoder->encodePassword($user, $password));
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($user);
				$entityManager->flush();
				$this->sendMail($user, $password);
				return $this->redirectToRoute('user_resetted');
			} else {
				$error = true;
			}
		}

		return $this->render('user/reset.html.twig', ['form' => $form->createView(), 'error' => $error]);
	}

	/**
	 * @Route("/user/resetted", name="user_resetted")
	 *
	 * @return Response
	 */
	public function resetted(): Response {
		return $this->render('user/resetted.html.twig');
	}

	/**
	 * @Route("/user/logout", name="user_logout")
	 * @IsGranted("ROLE_USER")
	 *
	 * @return Response
	 */
    public function logout(): Response {
    	return $this->redirectToRoute('index');
	}

	/**
	 * @param User $user
	 * @param string $password
	 */
	private function sendMail(User $user, string $password) {
		$mail = new Email();
		$mail->from(new Address($this->getParameter('app.mail.admin.address'), $this->getParameter('app.mail.admin.name')));
		$mail->to(new Address($user->getEmail(), $user->getName()));
		$mail->subject('Fantasya-Registrierung');
		$mail->text($this->renderView('emails/user_reset.html.twig', ['user' => $user, 'password' => $password]));
		$this->mailer->send($mail);
	}

	/**
	 * @param User $user
	 */
	private function sendAdminMail(User $user) {
		$mail = new Email();
		$mail->from(new Address($this->getParameter('app.mail.admin.address'), $this->getParameter('app.mail.admin.name')));
		$mail->to(new Address($this->getParameter('app.mail.game.address'), $this->getParameter('app.mail.game.name')));
		$mail->subject('Neue Fantasya-Registrierung');
		$mail->text($this->renderView('emails/admin_user.html.twig', ['user' => $user]));
		try {
			$this->mailer->send($mail);
		} catch (\Throwable $e) {
		}
	}
}

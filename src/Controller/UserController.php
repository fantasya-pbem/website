<?php
declare (strict_types = 1);
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * @param UserRepository $repository
	 */
	public function __construct(UserRepository $repository, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer) {
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
	 * @Route("/user/register", name="user_register")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function register(Request $request): Response {
		$form         = $this->createForm(RegistrationType::class, new Registration());
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
				$password = uniqid();
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
		$mail = new \Swift_Message();
		$mail->setFrom('admin@fantasya-pbem.de', 'Fantasya-Administrator');
		$mail->setTo($user->getEmail(), $user->getName());
		$mail->setSubject('Fantasya-Registrierung');
		$mail->setBody($this->renderView('emails/user_reset.html.twig', [
			'user'     => $user,
			'password' => $password
		]));
		$this->mailer->send($mail);
	}
}

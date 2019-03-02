<?php
declare (strict_types = 1);
namespace App\Controller;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
	/**
	 * @var array(string=>string)
	 */
	private static $roles = [
		User::ROLE_ADMIN        => 'Administrator',
		User::ROLE_BETA_TESTER  => 'Betatester',
		User::ROLE_MULTI_PLAYER => 'Mehrere Parteien',
		User::ROLE_NEWS_CREATOR => 'News verfassen'
	];

	/**
	 * @var array(int=>string)
	 */
	private static $errors = [
		10 => 'Der Benutzername darf nicht leer sein.',
		11 => 'Der Benutzername darf nicht länger als 190 Zeichen sein.',
		12 => 'Es gibt bereits einen Benutzer mit diesem Namen.',
		20 => 'Das ist keine gültige E-Mail-Adresse.',
		21 => 'Die E-Mail-Adresse darf nicht länger als 190 Zeichen sein.',
		22 => 'Es gibt bereits einen Benutzer mit dieser E-Mail-Adresse.',
		30 => 'Das Passwort darf nicht leer sein.'
	];

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
	 * @Route("/profile", name="profile")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function profile(Request $request): Response {
		$roles = [];
		foreach ($this->user()->getRoles() as $role) {
			if ($role !== User::ROLE_USER) {
				$roles[] = self::$roles[$role] ?? $role;
			}
		}

		$success = null;
		$error   = null;
		if ($request->query->has('error')) {
			$errorCode = (int)$request->query->get('error');
			if ($errorCode) {
				$error = $errorCode;
			} else {
				$success = true;
			}
		}

		return $this->render('profile/index.html.twig', [
			'roles'   => $roles,
			'success' => $success,
			'error'   => self::$errors[$error] ?? null
		]);
	}

	/**
	 * @Route("/profile/change", name="profile_change")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function change(Request $request): Response {
		if ($request->request->has('submitName') && $request->request->has('name')) {
			$name = $request->request->get('name');
			if ($name) {
				if (mb_strlen($name) <= 190) {
					$user = $this->user();
					if ($name !== $user->getName()) {
						if ($this->repository->findOneBy(['name' => $name])) {
							$error = 12;
						} else {
							$this->save($this->user()->setName($name));
							$error = 0;
						}
					}
				} else {
					$error = 11;
				}
			} else {
				$error = 10;
			}
		}

		if ($request->request->has('submitEmail') && $request->request->has('email')) {
			$email     = strtolower($request->request->get('email'));
			$validator = new EmailValidator();
			if ($validator->isValid($email, new RFCValidation())) {
				if (strlen($email) <= 190) {
					$user = $this->user();
					if ($email !== $user->getEmail()) {
						if ($this->repository->findOneBy(['email' => $email])) {
							$error = 22;
						} else {
							$this->save($this->user()->setEmail($email));
							$error = 0;
						}
					}
				} else {
					$error = 21;
				}
			} else {
				$error = 20;
			}
		}

		if ($request->request->has('submitPassword') && $request->request->has('password')) {
			$password = $request->request->get('password');
			if ($password) {
				$user = $this->user();
				$user->setPassword($this->passwordEncoder->encodePassword($user, $password));
				$this->save($user);
				$error = 0;
			} else {
				$error = 30;
			}
		}

		return $this->redirectToRoute('profile', isset($error) ? ['error' => $error] : []);
	}

	/**
	 * @return User
	 */
	private function user(): User {
		return $this->getUser();
	}

	/**
	 * @param User $user
	 */
	private function save(User $user) {
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user);
		$entityManager->flush();
		$this->sendMail($user);
	}

	/**
	 * @param User $user
	 */
	private function sendMail(User $user) {
		$mail = new \Swift_Message();
		$mail->setFrom('admin@fantasya-pbem.de', 'Fantasya-Administrator');
		$mail->setTo($user->getEmail(), $user->getName());
		$mail->setSubject('Fantasya-Profil geändert');
		$mail->setBody($this->renderView('emails/profile_change.html.twig', [
			'user' => $user
		]));
		$this->mailer->send($mail);
	}
}

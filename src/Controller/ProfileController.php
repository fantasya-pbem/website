<?php
declare (strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use JetBrains\PhpStorm\ArrayShape;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Service\GameService;
use App\Service\MailService;
use App\Service\PartyService;
use App\Repository\UserRepository;

/**
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
	/**
	 * @var array(string=>string)
	 */
	private static array $roles = [
		User::ROLE_ADMIN        => 'Administrator',
		User::ROLE_BETA_TESTER  => 'Betatester',
		User::ROLE_MULTI_PLAYER => 'Mehrere Parteien',
		User::ROLE_NEWS_CREATOR => 'News verfassen'
	];

	/**
	 * @var array(int=>string)
	 */
	private static array $errors = [
		10 => 'Der Benutzername darf nicht leer sein.',
		11 => 'Der Benutzername darf nicht länger als 190 Zeichen sein.',
		12 => 'Es gibt bereits einen Benutzer mit diesem Namen.',
		20 => 'Das ist keine gültige E-Mail-Adresse.',
		21 => 'Die E-Mail-Adresse darf nicht länger als 190 Zeichen sein.',
		22 => 'Es gibt bereits einen Benutzer mit dieser E-Mail-Adresse.',
		30 => 'Das Passwort darf nicht leer sein.',
		40 => 'Die Einstellungen konnten nicht gespeichert werden.'
	];

	private EntityManager $entityManager;

	public function __construct(private UserRepository $userRepository, private GameService $gameService,
								private PartyService $partyService, private MailService $mailService,
								private UserPasswordHasherInterface $passwordHasher, ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
	}

	/**
	 * @Route("/profile", name="profile")
	 * @throws \Exception
	 */
	public function index(Request $request): Response {
		$roles   = $this->getRoles();
		$flags   = $this->getFlags();
		$games   = $this->gameService->getAll();
		$parties = $this->partyService->getFor($this->user());
		$newbies = $this->partyService->getNewbies($this->user());

		$success   = null;
		$errorCode = 0;
		$error     = null;
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
			'flags'   => $flags,
			'games'   => $games,
			'parties' => $parties,
			'newbies' => $newbies,
			'success' => $success,
			'error'   => ['code' => $errorCode, 'text' => self::$errors[$error] ?? null]
		]);
	}

	/**
	 * @Route("/profile/change", name="profile_change")
	 * @throws \Throwable
	 */
	public function change(Request $request): Response {
		if ($request->request->has('submitName') && $request->request->has('name')) {
			$name = $request->request->get('name');
			if ($name) {
				if (mb_strlen($name) <= 190) {
					$user = $this->user();
					if ($name !== $user->getName()) {
						if ($this->userRepository->findOneBy(['name' => $name])) {
							$error = 12;
						} else {
							$this->save($this->user()->setName($name), true);
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
						if ($this->userRepository->findOneBy(['email' => $email])) {
							$error = 22;
						} else {
							$this->save($this->user()->setEmail($email), true);
							$this->partyService->update($this->user());
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
				$user->setPassword($this->passwordHasher->hashPassword($user, $password));
				$this->save($user, true);
				$error = 0;
			} else {
				$error = 30;
			}
		}

		return $this->redirectToRoute('profile', isset($error) ? ['error' => $error] : []);
	}

	/**
	 * @Route("/profile/settings", name="profile_settings")
	 * @throws \Throwable
	 */
	public function settings(Request $request): Response {
		if ($request->request->has('submitSettings') && $request->request->has('flags')) {
			$user  = $this->user();
			$flags = $request->request->get('flags');

			try {
				$withAttachment = $flags['withAttachment'] ?? false;
				$user->setFlag(User::FLAG_WITH_ATTACHMENT, (bool)$withAttachment);

				$this->save($user);
				$error = 0;
			} catch (\Exception) {
				$error = 40;
			}
		}

		return $this->redirectToRoute('profile', isset($error) ? ['error' => $error] : []);
	}

	/**
	 * @return string[]
	 */
	private function getRoles(): array {
		$roles = [];
		foreach ($this->user()->getRoles() as $role) {
			if ($role !== User::ROLE_USER) {
				$roles[] = self::$roles[$role] ?? $role;
			}
		}
		return $roles;
	}

	/**
	 * @return array(string=>string)
	 */
	#[ArrayShape(['withAttachment' => 'string'])]
	private function getFlags(): array {
		$withAttachment = $this->user()->hasFlag(User::FLAG_WITH_ATTACHMENT);
		return [
			'withAttachment' => $withAttachment ? ' checked="checked"' : ''
		];
	}

	/** @noinspection PhpUnnecessaryLocalVariableInspection */
	private function user(): User {
		/** @var User $user */
		$user = $this->getUser();
		return $user;
	}

	/**
	 * @throws \Throwable
	 */
	private function save(User $user, bool $sendMail = false) {
		$this->entityManager->persist($user);
		$this->entityManager->flush();
		if ($sendMail) {
			$this->sendMail($user);
		}
	}

	/**
	 * @throws \Throwable
	 */
	private function sendMail(User $user) {
		$mail = $this->mailService->fromAdmin($user);
		$mail->subject('Fantasya-Profil geändert');
		$mail->text($this->renderView('emails/profile_change.html.twig', ['user' => $user]));
		$this->mailService->signAndSend($mail);
	}
}

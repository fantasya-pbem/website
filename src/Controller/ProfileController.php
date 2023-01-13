<?php
declare(strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\Game;
use App\Entity\User;
use App\Game\Newbie;
use App\Game\Party;
use App\Service\GameService;
use App\Service\MailService;
use App\Service\PartyService;
use App\Repository\UserRepository;
use App\Security\Role;

#[IsGranted(Role::USER)]
class ProfileController extends AbstractController
{
	/**
	 * @var array<string, string>
	 */
	private static array $roles = [
		Role::ADMIN        => 'Administrator',
		Role::BETA_TESTER  => 'Betatester',
		Role::MULTI_PLAYER => 'Mehrere Parteien',
		Role::NEWS_CREATOR => 'News verfassen'
	];

	/**
	 * @var array<int, string>
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

	public function __construct(private readonly UserRepository $userRepository, private readonly GameService $gameService,
								private readonly PartyService $partyService, private readonly MailService $mailService,
								private readonly UserPasswordHasherInterface $passwordHasher, readonly ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
	}

	/**
	 * @throws \Exception
	 */
	#[Route('/profil', 'profile')]
	public function index(Request $request): Response {
		$roles   = $this->getRoles();
		$flags   = $this->getFlags();
		$games   = $this->gameService->getAll();
		$parties = $this->partyService->getFor($this->user());
		$newbies = $this->partyService->getNewbies($this->user());
		$this->removeEmptyGameParties($games, $parties, $newbies);

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
	 * @throws \Throwable
	 */
	#[Route('/profil-aendern', 'profile_change')]
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
	 * @throws \Throwable
	 */
	#[Route('/profil/einstellungen', 'profile_settings')]
	public function settings(Request $request): Response {
		if ($request->request->has('submitSettings')) {
			$user = $this->user();
			try {
				$withAttachment = $request->request->has('withAttachment');
				$user->setFlag(User::FLAG_WITH_ATTACHMENT, $withAttachment);
				$this->save($user);
				$error = 0;
			} catch (\Exception) {
				$error = 40;
			}
		}

		return $this->redirectToRoute('profile', isset($error) ? ['error' => $error] : []);
	}

	/**
	 * @param array<Game> $games
	 * @param array<int, array<Party>> $parties
	 * @param array<int, array<Newbie>> $newbies
	 */
	private function removeEmptyGameParties(array &$games, array &$parties, array &$newbies): void {
		$gameById = [];
		foreach ($games as $i => $game) {
			$gameById[$game->getId()] = $i;
		}
		foreach ($gameById as $id => $i) {
			if (empty($parties[$id]) && empty($newbies[$id])) {
				unset($games[$i]);
				unset($parties[$id]);
				unset($newbies[$id]);
			}
		}
		$games = array_values($games);
	}

	/**
	 * @return array<string>
	 */
	private function getRoles(): array {
		$roles = [];
		foreach ($this->user()->getRoles() as $role) {
			if ($role !== Role::USER) {
				$roles[] = self::$roles[$role] ?? $role;
			}
		}
		return $roles;
	}

	/**
	 * @return array<string, string>
	 */
	#[ArrayShape(['withAttachment' => 'string'])]
	private function getFlags(): array {
		$withAttachment = $this->user()->hasFlag(User::FLAG_WITH_ATTACHMENT);
		return [
			'withAttachment' => $withAttachment ? ' checked="checked"' : ''
		];
	}

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

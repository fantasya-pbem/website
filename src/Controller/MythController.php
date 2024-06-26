<?php
declare(strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\Myth;
use App\Entity\User;
use App\Form\MythType;
use App\Repository\MythRepository;
use App\Security\Role;
use App\Service\MailService;

class MythController extends AbstractController
{
	private EntityManagerInterface $entityManager;

	public function __construct(private readonly MythRepository $repository, private readonly MailService $mailService,
		                        ManagerRegistry $managerRegistry) {
		$this->entityManager = $managerRegistry->getManager();
	}

	#[Route('/geruechte/{page}', 'myth', ['page' => '\d+'])]
	public function index(int $page = 1): Response {
		if ($page <= 0) {
			$page = 1;
		}
		$myths = $this->repository->getAll($page);
		$next  = count($myths) > $page * MythRepository::PAGE_SIZE ? $page + 1 : $page;

		return $this->render('myth/index.html.twig', ['myths' => $myths, 'page' => $page, 'next' => $next]);
	}

	#[IsGranted(Role::USER)]
	#[Route('/geruecht-verbreiten', 'myth_spread')]
	public function spread(Request $request): Response {
		$myth = new Myth();
		$form = $this->createForm(MythType::class, $myth);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$myth = $form->getData();
			$this->entityManager->persist($myth);
			$this->entityManager->flush();
			$this->sendAdminMail($myth);
			return $this->redirectToRoute('myth');
		}

		return $this->render('myth/spread.html.twig', ['form' => $form->createView()]);
	}

	private function user(): User {
		/** @var User $user */
		$user = $this->getUser();
		return $user;

	}

	private function sendAdminMail(Myth $myth): void {
		$mail = $this->mailService->toGameMaster();
		$mail->subject('Es geht ein Gerücht um');
		$mail->text($this->renderView('emails/admin_myth.html.twig', ['user' => $this->user(), 'myth' => $myth]));
		try {
			$this->mailService->signAndSend($mail);
		} catch (\Throwable) {
		}
	}
}

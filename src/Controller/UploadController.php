<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Exception\OrderException;
use App\Game\OrderTrait;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\EngineService;
use App\Service\OrderService;
use App\Service\PartyService;

class UploadController extends AbstractController
{
	use OrderTrait;

	public function __construct(private readonly UserRepository $userRepository, private readonly GameRepository $gameRepository,
		                        private readonly PartyService $partyService, private readonly OrderService $orderService,
		                        private readonly EngineService $engineService, private readonly UserPasswordHasherInterface $hasher) {
	}

	/**
	 * @noinspection PhpConditionAlreadyCheckedInspection
	 */
	#[Route('/magellan/{alias}', 'upload')]
	public function index(string $alias): Response {
		$this->game = $this->gameRepository->findByAlias($alias);
		if (!$this->game) {
			return new Response(status: Response::HTTP_NOT_FOUND);
		}
		$file = count($_FILES) === 1 ? current($_FILES) : null;
		if (!$file || $file['error'] !== 0 || $file['size'] <= 0 || !is_file($file['tmp_name'])) {
			return new Response(status: Response::HTTP_BAD_REQUEST);
		}

		$this->content = trim(file_get_contents($file['tmp_name']));
		if (!$this->content) {
			return new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
		}

		try {
			$this->fetchUserParty();
			$this->getRound();
			$this->saveOrders();
		} catch (OrderException $e) {
			return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		return new Response('OK');
	}
}

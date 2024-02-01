<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Exception\OrderException;
use App\Game\OrderTrait;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\CheckService;
use App\Service\EngineService;
use App\Service\OrderService;
use App\Service\PartyService;

class UploadController extends AbstractController
{
	use OrderTrait;

	public function __construct(private readonly UserRepository $userRepository, private readonly GameRepository $gameRepository,
		                        private readonly PartyService $partyService, private readonly OrderService $orderService,
		                        private readonly EngineService $engineService, private readonly CheckService $checkService,
		                        private readonly UserPasswordHasherInterface $hasher) {
	}

	/**
	 * @noinspection PhpConditionAlreadyCheckedInspection
	 */
	#[Route('/magellan/{alias}', 'upload')]
	public function index(Request $request, string $alias): Response {
		if ($request->getMethod() !== Request::METHOD_POST) {
			return $this->render('upload/magellan.html.twig', response: new Response(status: Response::HTTP_METHOD_NOT_ALLOWED));
		}

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

		if ($request->query->has('nosimulation')) {
			$noSimulation = $request->query->get('nosimulation');
			if (strlen($noSimulation) <= 15 && preg_match('/^[a-zA-Z,]+$/', $noSimulation) === 1) {
				return $this->returnCheckResponse(explode(',', strtoupper($noSimulation)));
			}
		}
		return $this->returnCheckResponse();
	}

	protected function returnCheckResponse(array $noSimulation = []): Response {
		$check = $this->getCheckResult();
		if (empty($check)) {
			$code   = Response::HTTP_OK;
			$result = 'Die Schreibweise der Befehle scheint in Ordnung zu sein.';
		} else {
			$code   = Response::HTTP_CREATED;
			$result = implode(PHP_EOL, $check);
		}

		if (!in_array('ALL', $noSimulation) && !in_array('1', $noSimulation)) {
			$simulation = $this->getSimulationProblems($noSimulation);
			if (is_array($simulation)) {
				if (empty($simulation)) {
					$result .= PHP_EOL . 'Die Simulation hat keine Probleme aufgezeigt.';
				} else {
					$code = Response::HTTP_CREATED;
					foreach ($simulation as $line) {
						$result .= PHP_EOL . $line;
					}
				}
			}
		}

		return new Response($result, $code);
	}
}

<?php
declare(strict_types = 1);
namespace App\Controller;

use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Data\Order;
use App\Entity\User;
use App\Game\Party;
use App\Game\Turn;
use App\Security\Role;
use App\Service\EngineService;
use App\Service\GameService;
use App\Service\OrderService;
use App\Service\PartyService;

#[IsGranted(Role::USER)]
class OrderController extends AbstractController
{
	public function __construct(private readonly GameService $gameService, private readonly PartyService $partyService,
								private readonly OrderService $orderService, private readonly EngineService $engineService) {
	}

	/**
	 * @throws \Exception
	 */
	#[Route('/befehle-kontrollieren/{id}/{turn}', 'order')]
	public function index(Request $request, string $id = '', int $turn = 0): Response {
		$parties = $this->parties();
		if (empty($parties) || $id && !isset($parties[$id])) {
			return $this->redirectToRoute('profile');
		}

		if (!$id) {
			$id = key($parties);
		}
		if (!$turn) {
			$turn = $this->turn($request);
		}

		$party         = $parties[$id];
		$game          = $this->gameService->getCurrent();
		$engine        = $this->engineService->get($game);
		$turns         = $this->getTurns($turn);
		$round         = $engine->getRound($game);
		$hasSimulation = $engine->canSimulate($game, $turn) && $this->getParameter('app.simulation');

		$order = new Order();
		$order->setParty($party->getOwner());
		$order->setTurn($turn);
		$order->setGame($game);
		$this->orderService->setContext($order);

		return $this->render('order/index.html.twig', [
			'id' => $id,
			'turn' => $turn,
			'hasSimulation' => $hasSimulation,
			'round' => $round,
			'parties' => $parties,
			'turns' => $turns
		]);
	}

	#[Route('/befehle-simulieren', 'order_simulation')]
	public function simulation(Request $request): Response {
		$parties = $this->partyService->getCurrent($this->user());
		if (empty($parties)) {
			return new Response(status: Response::HTTP_NOT_FOUND);
		}

		$game  = $this->gameService->getCurrent();
		$party = $parties[0];
		$order = new Order();
		$order->setParty($party->getOwner());
		$order->setTurn($this->turn($request));
		$order->setGame($game);
		$this->orderService->setContext($order);
		$simulation = $this->orderService->getSimulation();

		return new Response(content: $simulation, headers: ['Content-Type' => 'text/plain']);
	}

	/**
	 * @throws \Exception
	 */
	#[Route('/befehle-senden', 'order_send')]
	public function send(Request $request): Response {
		$parties = $this->partyService->getCurrent($this->user());
		if (empty($parties)) {
			return $this->redirectToRoute('profile');
		}

		$turn = $this->turn($request);
		$form = $this->createOrderForm(new Order(), $parties, $turn);
		$form->handleRequest($request);

		$isWrongParty = false;
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Order $order */
			$order = $form->getData();
			$game  = $this->gameService->getCurrent();
			$id    = $order->getPartyId();
			$party = null;
			if ($id === null) {
				$party = $this->partyService->getByOwner($order->getParty(), $game);
			} else {
				try {
					$party = $this->partyService->getById($id, $game);
				} catch (\Throwable) {
				}
			}
			if ($order->getParty() === $party?->getOwner()) {
				$order->setGame($game);
				$this->orderService->setContext($order);
				$this->orderService->saveOrders();
				return $this->redirectToRoute('order', ['id' => $party->getId(), 'turn' => $turn]);
			}

			$isWrongParty = true;
		}

		return $this->render('order/send.html.twig', ['form' => $form->createView(), 'isWrongParty' => $isWrongParty]);
	}

	private function user(): User {
		/** @var User $user */
		$user = $this->getUser();
		return $user;
	}

	private function parties(): array {
		$parties = [];
		foreach ($this->partyService->getCurrent($this->user()) as $party) {
			$parties[$party->getId()] = $party;
		}
		return $parties;
	}

	/**
	 * @throws \Exception
	 */
	private function turn(Request $request): int {
		$turn  = new Turn($this->gameService->getCurrent(), $this->engineService);
		$round = $turn->getRound();
		if ($request->request->has('form')) {
			$form = $request->request->all('form');
			if (isset($form['turn'])) {
				$r = (int)$form['turn'];
				if ($r > 0) {
					$round = $r;
				}
			}
		}
		return $round;
	}

	/**
	 * @param array<Party> $parties
	 * @throws \Exception
	 */
	private function createOrderForm(Order $order, array $parties, int $turn): FormInterface {
		$form = $this->createFormBuilder($order);
		$form->add('party', ChoiceType::class, [
			'label'   => 'Partei',
			'choices' => $this->getParties($parties),
			'attr'    => ['tabindex' => 4]
		]);
		$form->add('turn', ChoiceType::class, [
			'label'   => 'Runde',
			'choices' => $this->getTurns($turn, 0),
			'data'    => (string)$turn,
			'attr'    => ['tabindex' => 3]
		]);
		$form->add('orders', TextareaType::class, [
			'label' => 'Befehle',
			'attr'  => ['autofocus' => true, 'rows' => 10, 'tabindex' => 1]
		]);
		$form->add('submit', SubmitType::class, [
			'label' => 'Befehle senden',
			'attr'  => ['tabindex' => 2]
		]);
		return $form->getForm();
	}

	/**
	 * @param array<Party> $parties
	 * @return array<string>
	 */
	private function getParties(array $parties): array {
		$choices = [];
		foreach ($parties as $party) {
			$choices[$party->getName()] = $party->getOwner();
		}
		return $choices;
	}

	/**
	 * @return array<string>
	 */
	#[Pure] private function getTurns(int $turn, int $min = -3, int $max = 3): array {
		$turns = [];
		$next  = max(0, $turn + $min);
		$last  = $turn + $max;
		while ($next <= $last ) {
			$round = (string)$next;
			$next++;
			$turn         = (string)$next;
			$turns[$turn] = $round;
		}
		return $turns;
	}
}

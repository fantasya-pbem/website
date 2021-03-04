<?php
declare (strict_types = 1);
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Data\Order;
use App\Entity\User;
use App\Game\Party;
use App\Game\Turn;
use App\Service\GameService;
use App\Service\OrderService;
use App\Service\PartyService;

/**
 * @IsGranted("ROLE_USER")
 */
class OrderController extends AbstractController
{
	public function __construct(private GameService $gameService, private PartyService $partyService,
								private OrderService $orderService, private EntityManagerInterface $manager) {
	}

	/**
	 * @Route("/order", name="order")
	 * @throws \Exception
	 */
	public function index(Request $request): Response {
		$parties = $this->partyService->getCurrent($this->user());
		if (empty($parties)) {
			return $this->redirectToRoute('profile');
		}

		$party = $parties[0];
		$turn  = $this->turn($request);
		$order = new Order();
		$form  = $this->createOrderForm($order, $parties, $turn);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var Order $order */
			$order = $form->getData();
		} else {
			$order->setParty($party->getOwner());
			$order->setTurn($turn);
		}
		$order->setGame($this->gameService->getCurrent()->getAlias());
		$this->orderService->setContext($order);

		return $this->render('order/index.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @Route("/order/send", name="order_send")
	 * @throws \Exception
	 */
	public function send(Request $request): Response {
		$parties = $this->partyService->getCurrent($this->user());
		if (empty($parties)) {
			return $this->redirectToRoute('profile');
		}

		$turn = $this->turn($request);
		$form = $this->createSendForm(new Order(), $parties, $turn);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var Order $order */
			$order = $form->getData();
			$order->setGame($this->gameService->getCurrent()->getAlias());
			$this->orderService->setContext($order);
			$this->orderService->saveOrders();
			return $this->redirectToRoute('order_success', ['p' => $order->getParty(), 't' => $turn]);
		}

		return $this->render('order/send.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @Route("/order/party/{p}/turn/{t}", name="order_success")
	 * @throws \Exception
	 */
	public function party(string $p, int $t): Response {
		$parties = $this->partyService->getCurrent($this->user());
		$party   = null;
		foreach ($parties as $userParty) {
			if ($userParty->getOwner() === $p) {
				$party = $p;
				break;
			}
		}
		if (!$party) {
			return $this->redirectToRoute('profile');
		}

		$order = new Order();
		$order->setParty($p);
		$order->setTurn($t);
		$order->setGame($this->gameService->getCurrent()->getAlias());
		$this->orderService->setContext($order);
		$form = $this->createOrderForm($order, $parties, $t);

		return $this->render('order/index.html.twig', ['form' => $form->createView()]);
	}

	private function user(): User {
		/** @var User $user */
		$user = $this->getUser();
		return $user;

	}

	/**
	 * @throws \Exception
	 */
	private function turn(Request $request): int {
		$turn  = new Turn($this->gameService->getCurrent(), $this->manager->getConnection());
		$round = $turn->getRound();
		if ($request->request->has('form')) {
			$form = $request->request->get('form');
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
	 * @param Party[] $parties
	 * @throws \Exception
	 */
	private function createOrderForm(Order $order, array $parties, int $turn): FormInterface {
		$form = $this->createFormBuilder($order);
		$form->setAction($this->generateUrl('order'));
		$form->add('party', ChoiceType::class, [
			'label'   => 'Partei',
			'choices' => $this->getParties($parties)
		]);
		$form->add('turn', ChoiceType::class, [
			'label'   => 'Runde',
			'choices' => $this->getTurns($turn),
			'data'    => (string)$turn
		]);
		$form->add('submit', SubmitType::class, [
			'label' => 'Anzeigen'
		]);
		return $form->getForm();
	}

	/**
	 * @param Party[] $parties
	 * @throws \Exception
	 */
	private function createSendForm(Order $order, array $parties, int $turn): FormInterface {
		$form = $this->createFormBuilder($order);
		$form->add('party', ChoiceType::class, [
			'label'   => 'Partei',
			'choices' => $this->getParties($parties)
		]);
		$form->add('turn', ChoiceType::class, [
			'label'   => 'Runde',
			'choices' => $this->getTurns($turn, 0),
			'data'    => (string)$turn
		]);
		$form->add('orders', TextareaType::class, [
			'label' => 'Befehle',
			'attr'  => ['rows' => 10]
		]);
		$form->add('submit', SubmitType::class, [
			'label' => 'Befehle senden'
		]);
		return $form->getForm();
	}

	/**
	 * @param Party[] $parties
	 * @return string[]
	 */
	private function getParties(array $parties): array {
		$choices = [];
		foreach ($parties as $party) {
			$choices[$party->getName()] = $party->getOwner();
		}
		return $choices;
	}

	/**
	 * @return string[]
	 */
	private function getTurns(int $turn, int $min = -5, int $max = 5): array {
		$turns = [];
		$next  = $turn + $min;
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

<?php
declare (strict_types = 1);
namespace App\Controller;

use Doctrine\DBAL\DBALException;
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
	/**
	 * @var GameService
	 */
	private $gameService;

	/**
	 * @var PartyService
	 */
	private $partyService;

	/**
	 * @var OrderService
	 */
	private $orderService;

	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * @param GameService $gameService
	 * @param PartyService $partyService
	 * @param OrderService $orderService
	 * @param EntityManagerInterface $manager
	 */
	public function __construct(GameService $gameService, PartyService $partyService, OrderService $orderService,
								EntityManagerInterface $manager) {
		$this->gameService  = $gameService;
		$this->partyService = $partyService;
		$this->orderService = $orderService;
		$this->manager      = $manager;
	}

	/**
	 * @Route("/order", name="order")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws DBALException
	 */
	public function index(Request $request): Response {
		$parties = $this->partyService->getCurrent($this->user());
		if (empty($parties)) {
			return $this->redirectToRoute('profile');
		}

		$party = $parties[0];
		$turn  = $this->turn($request, -5);
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
	 *
	 * @param Request $request
	 * @return Response
	 * @throws DBALException
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
	 *
	 * @param int $p
	 * @param int $t
	 * @return Response
	 * @throws DBALException
	 */
	public function party(int $p, int $t): Response {
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

	/**
	 * @return User
	 */
	private function user(): User {
		return $this->getUser();
	}

	/**
	 * @param Request $request
	 * @param int $min
	 * @return int
	 * @throws DBALException
	 */
	private function turn(Request $request, int $min = 0): int {
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
	 * @param Order $order
	 * @param array $parties
	 * @param int $turn
	 * @return FormInterface
	 * @throws DBALException
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
	 * @param Order $order
	 * @param array $parties
	 * @param int $turn
	 * @return FormInterface
	 * @throws DBALException
	 */
	private function createSendForm(Order $order, array $parties, int $turn): FormInterface {
		$form = $this->createFormBuilder($order);
		$form->add('party', ChoiceType::class, [
			'label'   => 'Partei',
			'choices' => $this->getParties($parties)
		]);
		$form->add('turn', ChoiceType::class, [
			'label'   => 'Runde',
			'choices' => $this->getTurns($turn, 0, 5),
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
	 * @throws DBALException
	 */
	private function getParties(array $parties): array {
		$choices = [];
		foreach ($parties as $party) {
			$choices[$party->getName()] = $party->getOwner();
		}
		return $choices;
	}

	/**
	 * @param int $turn
	 * @param int|null $min
	 * @param int|null $max
	 * @return string[]
	 */
	private function getTurns(int $turn, int $min = -5, int $max = 5): array {
		$turns = [];
		$next  = $turn + $min;
		$last  = $turn + $max;
		while ($next <= $last ) {
			$turn         = (string)$next;
			$turns[$turn] = $turn;
			$next++;
		}
		return $turns;
	}
}

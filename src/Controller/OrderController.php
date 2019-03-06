<?php
declare (strict_types = 1);
namespace App\Controller;

use App\Game\Party;
use App\Service\OrderService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Data\Order;
use App\Entity\User;
use App\Game\Turn;
use App\Service\GameService;
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
		$turn  = $this->turn($request);
		$order = new Order();
		$form  = $this->createOrderForm($order, $parties, $turn);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/* @var Order $order */
			$order = $form->getData();
		} else {
			$order->setParty($party->getId());
			$order->setTurn($turn);
		}
		$order->setGame($this->gameService->getCurrent()->getAlias());

		return $this->render('order/index.html.twig', [
			'form'  => $form->createView(),
			'order' => $order
		]);
	}

	/**
	 * @Route("/order/send", name="order_send")
	 *
	 * @return Response
	 */
	public function send(): Response {
		return $this->render('order/send.html.twig');
	}

	/**
	 * @return User
	 */
	private function user(): User {
		return $this->getUser();
	}

	/**
	 * @param Request $request
	 * @return int
	 * @throws DBALException
	 */
	private function turn(Request $request): int {
		if ($request->request->has('form')) {
			$form = $request->request->get('form');
			if (isset($form['turn'])) {
				return (int)$form['turn'];
			}
		}
		$turn = new Turn($this->gameService->getCurrent(), $this->manager->getConnection());
		return $turn->getRound();
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
	 * @return string[]
	 * @throws DBALException
	 */
	private function getParties(array $parties): array {
		$choices = [];
		foreach ($parties as $party) {
			$choices[$party->getName()] = $party->getId();
		}
		return $choices;
	}

	/**
	 * @param int $turn
	 * @return string[]
	 */
	private function getTurns(int $turn): array {
		$turns = [];
		$next  = $turn - 5;
		$last  = $turn + 5;
		while ($next <= $last ) {
			$turn         = (string)$next;
			$turns[$turn] = $turn;
			$next++;
		}
		return $turns;
	}
}

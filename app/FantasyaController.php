<?php

class FantasyaController extends BaseController {

	public function orders($party = null) {
		$game    = Game::current();
		$parties = Party::allFor(Auth::user());
		$parties = $parties[$game->id];
		if (count($parties) <= 0) {
			return Redirect::to('/login');
		}

		if (Request::isMethod('POST')) {
			$party = Input::get('party');
			$turn  = Input::get('turn');
		} else {
			$turn = Settings::on($game->database)->find('game.runde')->Value;
		}
		$party = $party ? $parties[$party] : current($parties);
		$order = new Order($game, $party, $turn);
		$p     = array();
		foreach ($parties as $id => $pty) {
			$p[$id] = $pty->name;
		}
		$turns = array();
		$next  = $turn - 5;
		$last  = $turn + 5;
		while ($next <= $last ) {
			$turns[$next++] = $next;
		}
		return View::make('orders', array('turn'  => $turn,  'turns'   => $turns,
										  'party' => $party, 'parties' => $p,
										  'orders' => $order->getOrders(),
										  'check'  => $order->fcheck()));
	}

	public function report() {
		$game    = Game::current();
		$parties = Party::allFor(Auth::user());
		$parties = $parties[$game->id];
		if (count($parties) <= 0) {
			return Redirect::to('/login');
		}

		$turn = Settings::on($game->database)->find('game.runde')->Value;
		if (Request::isMethod('POST')) {
			$party = Input::get('party');
			$t     = Input::get('turn');;
			if ($party && $t && isset($parties[$party])) {
				$party    = $party ? $parties[$party] : current($parties);
				$turn     = $t;
				$download = new Report($game, $party, $turn);
				if ($download->isValid()) {
					return Response::download($download->getPath());
				}
			}
		}
		$turns = array();
		$p     = array();
		foreach ($parties as $id => $pty) {
			$p[$id]     = $pty->name;
			$turns[$id] = Report::getTurns($game, $pty);
		}
		return View::make('report', array('turn'    => $turn, 'turns' => $turns,
		                                  'parties' => $p));
	}

	public function send($what) {
		//zur Zeit nur $what = orders
		$game    = Game::current();
		$party   = Party::allFor(Auth::user());
		$parties = $party[$game->id];
		if (Request::isMethod('POST')) {
			$p      = Input::get('party');
			$turn   = Input::get('turn');
			$orders = Input::get('orders');
			if ($p && $orders) {
				$order = new Order($game, $parties[$p], $turn);
				$order->setOrders($orders);
				return Redirect::to('/orders/' . $p);
			}
		}
		$p = array();
		foreach ($parties as $id => $party) {
			$p[$id] = $party->name;
		}
		$turn  = Settings::on($game->database)->find('game.runde')->Value;
		$turns = array();
		$next  = $turn;
		$last  = $turn + 4;
		while ($next <= $last ) {
			$turns[$next++] = $next;
		}
		return View::make('send-orders', array('parties' => $p, 'turns' => $turns));
	}
}

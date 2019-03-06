<?php

class FantasyaController extends BaseController {

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
}

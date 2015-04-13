<?php

class Game extends Eloquent {

	const TABLE = 'games';

	public $timestamps = false;

	public static function allById() {
		$games = array();
		foreach (self::all() as $game) {
			$games[$game->id] = $game;
		}
		return $games;
	}

	public static function current() {
		if (!Session::has('game')) {
			$parties = Party::allFor(Auth::user());
			if (count($parties) > 0) {
				$games = array_keys($parties);
				Session::put('game', $games[0]);
			}
		}
		return Game::find(Session::get('game'));
	}

}

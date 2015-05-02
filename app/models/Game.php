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
		if (Session::has('game')) {
			return Game::find(Session::get('game'));
		}
		foreach (Party::allFor(Auth::user()) as $id => $parties) {
			if (count($parties) > 0) {
				Session::put('game', $id);
				return Game::find($id);
			}
		}
		return null;
	}

}

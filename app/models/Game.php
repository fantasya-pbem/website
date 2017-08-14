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
			return self::find(Session::get('game'));
		}
		foreach (Party::allFor(Auth::user()) as $id => $parties) {
			if (count($parties) > 0) {
				Session::put('game', $id);
				return self::find($id);
			}
		}
		$games = self::allById();
		return current($games);
	}

	public static function next() {
		$current = self::current()->id;
		$all     = self::allById();
		$ids     = array_keys($all);
		$games   = array_values($all);
		$n       = count($all);
		for ($i = 0; $i < $n; $i++) {
			$id = $ids[$i];
			if ($id == $current) {
				return isset($games[$i + 1]) ? $games[$i + 1] : $games[0];
			}
		}
	}

}

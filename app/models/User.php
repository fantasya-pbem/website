<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	const CAN_CREATE_NEWS = 1;

	const CAN_BETA_TEST = 2;

	const CAN_PLAY_MULTIS = 4;

	public $timestamps = false;

	public static function has($flag) {
		$user = Auth::user();
		return $user && ($user->flags & $flag);
	}

	public static function countParties(Game $game = null) {
		$user    = Auth::user();
		$parties = Party::allFor($user);
		$id      = $game ? $game->id : null;
		return self::count($id, $parties);
	}

	public static function countNewParties(Game $game = null) {
		$user       = Auth::user();
		$newParties = NewParty::allFor($user);
		$id         = $game ? $game->id : null;
		return self::count($id, $newParties);
	}

	public static function countAllParties(Game $game = null) {
		return self::countParties($game) + self::countNewParties($game);
	}

	public static function canCreateMyths() {
		return isset($_ENV['MYTHS']) && $_ENV['MYTHS'];
	}

	public static function canRegister() {
		return isset($_ENV['REGISTRATION']) && $_ENV['REGISTRATION'];
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	protected static function count($id, array $parties) {
		$count = 0;
		if ($id !== null) {
			if (isset($parties[$id])) {
				$count = count($parties[$id]);
			}
		} else {
			foreach( $parties as $p ) {
				$count += count($p);
			}
		}
		return $count;
	}

}

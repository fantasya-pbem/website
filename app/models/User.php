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

	public static function countParties() {
		$user       = Auth::user();
		$parties    = Party::allFor($user);
		$newParties = NewParty::allFor($user);
		return count($parties) + count($newParties);
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

}

<?php

class Myth extends Eloquent {

	const TABLE = 'myth';

	protected $table = self::TABLE;

	public $timestamps = false;

	public static function getLast() {
		return DB::table(self::TABLE)->orderBy('id', 'DESC')->first()->myth;
	}

}

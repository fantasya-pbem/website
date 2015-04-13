<?php

class Weekday {

	protected static $weekday = array(
		0 => 'Sonntag',
		1 => 'Montag',
		2 => 'Dienstag',
		3 => 'Mittwoch',
		4 => 'Donnerstag',
		5 => 'Freitag',
		6 => 'Samstag',
		7 => 'Sonntag'
	);

	public static function translate($weekday) {
		return self::$weekday[(int)$weekday];
	}

}

<?php

class Report  {

	protected $game = '';

	protected $party = 0;

	protected $turn = 0;

	public function __construct(Game $game, Party $party, $turn) {
		$this->game  = $game;
		$this->party = $party;
		$this->turn  = $turn;
	}

	public function getPath() {
		$directory = self::getDirectory($this->game);
		return $directory . DIRECTORY_SEPARATOR . $this->turn . DIRECTORY_SEPARATOR . $this->turn . '-' . $this->party->id . '.zip';
	}

	public function isValid() {
		return is_file($this->getPath());
	}

	public static function getDirectory(Game $game) {
		return realpath(__DIR__ . '/../storage/zip') . DIRECTORY_SEPARATOR . $game->alias;
	}

	public static function getTurns(Game $game, Party $party) {
		$directories = glob(self::getDirectory($game) . '/*');
		$partyZip    = $party->id . '.zip';
		$turns       = array();
		foreach ($directories as $directory) {
			$turn = (int)basename($directory);
			if ($turn > 0) {
				if (is_file($directory . DIRECTORY_SEPARATOR . $turn . '-' . $partyZip)) {
					$turns[$turn] = $turn;
				}
			}
		}
		return $turns;
	}

}

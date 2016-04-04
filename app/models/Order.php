<?php

class Order  {

	protected $game = '';

	protected $party = 0;

	protected $turn = 0;

	public function __construct(Game $game, Party $party, $turn = null) {
		$this->game  = $game;
		$this->party = $party;
		$this->turn  = $turn > 0 ? (int)$turn : Settings::on($game->database)->find('game.runde')->Value;
	}

	public function getOrders() {
		$file = $this->getPath();
		if (is_file($file)) {
			return file_get_contents($file);
		}
		return '';
	}

	public function setOrders($orders) {
		$file = $this->getPath();
		$dir  = dirname($file);
		umask(0002);
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		return file_put_contents($file, $this->cleanUp($orders)) > 0;
	}

	public function getPath() {
		$storage = realpath(__DIR__ . '/../storage/orders');
		return $storage . DIRECTORY_SEPARATOR . $this->game->alias . DIRECTORY_SEPARATOR . $this->turn
						. DIRECTORY_SEPARATOR . $this->party->owner_id . '.order';
	}

    public function fcheck() {
        $check   = null;
        $command = isset($_ENV['FCHECK']) ? $_ENV['FCHECK'] : null;
        if (is_string($command) && strpos($command, '%input%') > 0 && strpos($command, '%output%') > 0) {
            $file = $this->getPath();
		    if (is_file($file)) {
			    $command = str_replace('%input%', $file, $command);
			    $output  = tempnam('/tmp', 'fcheck');
			    if ($output) {
			        $command = str_replace('%output%', $output, $command);
			        $result  = array();
			        $code    = -1;
			        exec($command, $result, $code);
			        if ($code === 0) {
			            $check = file_get_contents($output);
			        }
			        @unlink($output);
			    }
		    }
        }
        return $check;
    }

	private function cleanUp($orders) {
		$lines  = explode("\n", $orders);
		$orders = '';
		$n      = count($lines);
		if ($n > 0) {
			$first = strtoupper(trim($lines[0]));
			$parts = explode(' ', $first);
			if (count($parts) !== 3 || $parts[0] !== 'PARTEI' && $parts[0] !== 'FANTASYA' && $parts[0] !== 'ERESSEA') {
				$orders .= 'PARTEI ' . $this->party->id . ' ********' . PHP_EOL;
			}
			foreach ($lines as $line) {
				$orders .= trim($line) . PHP_EOL;
			}
		}
		return $orders;
	}

}

<?php
declare (strict_types = 1);
namespace App\Service;

use App\Data\Order;

/**
 * A service for templates to fetch myths.
 */
class OrderService
{
	/**
	 * @var string
	 */
	private $baseDir;

	/**
	 * Initialize order base directory.
	 */
	public function __construct() {
		$this->baseDir = realpath(__DIR__ . '/../../var/orders');
		if (!$this->baseDir) {
			throw new \RuntimeException('Orders directory not found.');
		}
	}

	/**
	 * @return string
	 */
	public function getPath(Order $order) {
		return $this->baseDir . DIRECTORY_SEPARATOR . $order->getGame() . DIRECTORY_SEPARATOR . $order->getTurn() .
			                    DIRECTORY_SEPARATOR . $order->getParty() . '.order';
	}

	/**
	 * @return string
	 */
	public function getOrders(Order $order) {
		$file = $this->getPath($order);
		if (is_file($file)) {
			$contents = file_get_contents($file);
			if ($contents) {
				return $contents;
			}
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getFcheck(Order $order) {
		$check   = null;
		$command = getenv('FCHECK') ?? null;
		if (is_string($command) && strpos($command, '%input%') > 0 && strpos($command, '%output%') > 0) {
			$file = $this->getPath($order);
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
					} else {
						if (count($result) === 3) {
							$result[1] = basename($result[1]);
							$check     = implode(' ', $result);
						}
					}
					@unlink($output);
				}
			}
		}
		return $check ? $check : '';
	}

	/**
	 * @param $orders
	 * @return bool
	 */
	public function setOrders($orders) {
		$file = $this->getPath();
		$dir  = dirname($file);
		umask(0002);
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		return file_put_contents($file, $this->cleanUp($orders)) > 0;
	}

	/**
	 * @param string[] $orders
	 * @return string
	 */
	private function cleanUp(array $orders): string {
		$lines  = explode("\n", $orders);
		$orders = '';
		$n      = count($lines);
		if ($n > 0) {
			$first = strtoupper(trim($lines[0]));
			$parts = explode(' ', $first);
			if (count($parts) !== 3 || $parts[0] !== 'PARTEI' && $parts[0] !== 'FANTASYA' && $parts[0] !== 'ERESSEA') {
				$orders .= 'PARTEI ' . $this->order->getParty() . ' "xxxxxxxx"' . PHP_EOL;
			}
			foreach ($lines as $line) {
				$orders .= trim($line) . PHP_EOL;
			}
		}
		return $orders;
	}
}

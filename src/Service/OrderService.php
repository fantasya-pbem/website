<?php
declare (strict_types = 1);
namespace App\Service;

use App\Data\Order;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * A service for fetching and storing Fantasya orders.
 */
class OrderService
{
	/**
	 * @var string
	 */
	private $baseDir;

	/**
	 * @var Order
	 */
	private $order;

	/**
	 * @var string
	 */
	private $fcheck;

	/**
	 * @param ContainerBagInterface $config
	 */
	public function __construct(ContainerBagInterface $config) {
		$this->baseDir = realpath(__DIR__ . '/../../var/orders');
		if (!$this->baseDir) {
			throw new \RuntimeException('Orders directory not found.');
		}
		$this->fcheck = $config->get('app.fcheck');
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->baseDir . DIRECTORY_SEPARATOR . $this->order->getGame() . DIRECTORY_SEPARATOR .
			   $this->order->getTurn() . DIRECTORY_SEPARATOR . $this->order->getParty() . '.order';
	}

	/**
	 * @return string
	 */
	public function getOrders() {
		$file = $this->getPath();
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
	public function getFcheck() {
		$check   = null;
		$command = $this->fcheck;
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
	 * @param Order $order
	 */
	public function setContext(Order $order) {
		$this->order = $order;
	}

	/**
	 * @return bool
	 */
	public function saveOrders() {
		$file = $this->getPath();
		$dir  = dirname($file);
		umask(0002);
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		return file_put_contents($file, $this->cleanUp()) > 0;
	}

	/**
	 * @return string
	 */
	private function cleanUp(): string {
		$lines  = explode("\n", $this->order->getOrders());
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

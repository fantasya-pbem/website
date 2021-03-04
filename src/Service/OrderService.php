<?php
declare (strict_types = 1);
namespace App\Service;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Data\Order;

/**
 * A service for fetching and storing Fantasya orders.
 */
class OrderService
{
	private string $baseDir;

	private Order $order;

	private string $fcheck;

	public function __construct(ContainerBagInterface $config) {
		$this->baseDir = realpath(__DIR__ . '/../../var/orders');
		if (!$this->baseDir) {
			throw new \RuntimeException('Orders directory not found.');
		}
		$this->fcheck = $config->get('app.fcheck');
	}

	#[Pure] public function getPath(): string {
		return $this->baseDir . DIRECTORY_SEPARATOR . $this->order->getGame() . DIRECTORY_SEPARATOR .
			   $this->order->getTurn() . DIRECTORY_SEPARATOR . $this->order->getParty() . '.order';
	}

	public function getOrders(): string {
		$file = $this->getPath();
		if (is_file($file)) {
			$contents = file_get_contents($file);
			if ($contents) {
				return $contents;
			}
		}
		return '';
	}

	public function getFcheck(): string {
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

	public function setContext(Order $order) {
		$this->order = $order;
	}

	public function saveOrders(): bool {
		$file = $this->getPath();
		$dir  = dirname($file);
		umask(0002);
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		return file_put_contents($file, $this->cleanUp()) > 0;
	}

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

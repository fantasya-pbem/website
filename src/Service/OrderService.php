<?php
declare(strict_types = 1);
namespace App\Service;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Data\Order;
use App\Repository\GameRepository;

class OrderService
{
	private const PARTY_LINE = ['PARTEI', 'FANTASYA', 'ERESSEA', 'LEMURIA'];

	private string $baseDir;

	private Order $order;

	private string $simulation;

	public function __construct(private readonly PartyService $service, private readonly CheckService $checkService,
								private readonly GameRepository $repository, ContainerBagInterface $config) {
		$this->baseDir = realpath(__DIR__ . '/../../var/orders');
		if (!$this->baseDir) {
			throw new \RuntimeException('Orders directory not found.');
		}
		$this->simulation = $config->get('app.simulation');
	}

	#[Pure] public function getPath(): string {
		return $this->baseDir . DIRECTORY_SEPARATOR . $this->order->getGame()->getAlias() . DIRECTORY_SEPARATOR .
			   $this->order->getTurn() . DIRECTORY_SEPARATOR . $this->order->getParty() . '.order';
	}

	public function getAvailable(): bool {
		$file = $this->getPath();
		return is_file($file);
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

	public function getCheck(): string {
		$rules = __DIR__ . '/../../var/check/' . $this->order->getGame()->getEngine() . '.tpl';
		$this->checkService->readRules($rules);
		$path = $this->getPath();
		if (!is_file($path)) {
			return 'Für Runde ' . $this->order->getTurn() . ' sind keine Befehle vorhanden.';
		}
		$commands = file_get_contents($path);
		$check    = $this->checkService->check($commands);
		return empty($check) ? 'Die Schreibweise der Befehle scheint in Ordnung zu sein.' : implode(PHP_EOL, $check);
	}

	public function getSimulation(): string {
		$check   = '';
		$command = $this->simulation ?? '';
		if (str_contains($command, '%uuid%')) {
			$command = str_replace('%uuid%', $this->order->getParty(), $command);
			$result  = array();
			exec($command, $result);
			$check = implode(PHP_EOL, $result);
		}
		return $check;
	}

	public function setContext(Order $order): void {
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
			$first = trim($lines[0]);
			$parts = explode(' ', $first);
			if (!in_array(strtoupper($parts[0]), self::PARTY_LINE)) {
				$orders .= 'PARTEI ' . $this->getPartyId() . ' "xxxxxxxx"' . PHP_EOL;
			} elseif (count($parts) < 2) {
				$lines[0] = $first . ' ' . $this->getPartyId() . ' "xxxxxxxx"';
			} elseif (count($parts) < 3) {
				$lines[0] = $first . ' "xxxxxxxx"';
			} else {
				$parts[2] = '"xxxxxxxx"';
				$lines[0] = implode(' ', $parts);
			}
			foreach ($lines as $line) {
				$orders .= trim($line) . PHP_EOL;
			}
		}
		return $orders;
	}

	private function getPartyId(): string {
		$owner = $this->order->getParty();
		$game  = $this->repository->findByAlias($this->order->getGame()->getAlias());
		return $this->service->getByOwner($owner, $game)->getId();
	}
}

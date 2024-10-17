<?php
/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */
declare(strict_types = 1);
namespace App\Game;

use App\Data\Order;
use App\Entity\Game;
use App\Entity\User;
use App\Exception\OrderException;

trait OrderTrait
{
	private User $user;

	private Game $game;

	private ?Party $party;

	private int $round;

	private string $content;

	/**
	 * @throws OrderException
	 * @noinspection PhpConditionAlreadyCheckedInspection
	 */
	private function fetchUserParty(): void {
		// Befehle extrahieren:
		$endOfLine = strpos($this->content, "\n");
		if (!$endOfLine) {
			throw new OrderException('Die Befehle bestehen nur aus einer Zeile.', 2);
		}
		$firstLine = substr($this->content, 0, $endOfLine);
		if (strlen($firstLine) <= 0) {
			throw new OrderException('Die erste Befehlszeile ist leer.', 2);
		}
		if (!preg_match('/^([^ ]+) +([a-zA-Z0-9]+) +"([^"]*)"( *;.*)?$/', $firstLine, $parts) || count($parts) < 3 + 1) {
			throw new OrderException('Die erste Befehlszeile ist fehlerhaft.', 2);
		}
		/** @noinspection PhpUnusedLocalVariableInspection */
		$clientGame = $parts[1]; //currently unused
		$party      = $parts[2];
		$password   = $parts[3];

		try {
			$this->party = $this->partyService->getById($party, $this->game);
		} catch (\Exception $e) {
			$message = 'Fehler beim Ermitteln der Partei ' . $party . ' im Spiel ' . $this->game->getName() . '.';
			throw new OrderException($message, 1, $e);
		}
		if (!$this->party) {
			$message = 'Die Partei ' . $party . ' existiert nicht im Spiel ' . $this->game->getName() . '.';
			throw new OrderException($message, 4);
		}

		$this->user = $this->userRepository->find($this->party->getUser());
		if (!$this->user) {
			throw new OrderException('Der Benutzer #' . $this->party->getUser() . ' wurde nicht gefunden.', 1);
		}
		if (!$this->hasher->isPasswordValid($this->user, $password)) {
			throw new OrderException('Das Kennwort ist falsch.', 4);
		}
	}

	/**
	 * @throws OrderException
	 */
	private function getRound(): void {
		try {
			$current = new Turn($this->game, $this->engineService);
		} catch (\Exception $e) {
			throw new OrderException('Die aktuelle Runde konnte nicht ermittelt werden.', 1, $e);
		}
		$this->round = $current->getRound();

		if (preg_match('/^RUNDE\h+(\d+)\s*$/im', $this->content, $matches)) {
			$maxRound = $this->round + 3;
			$round    = (int)$matches[1] - 1;
			if ($round >= $this->round && $round <= $maxRound) {
				$this->round = $round;
			} else {
				throw new OrderException('RUNDE ' . $round . ' ist nicht gültig.', 4);
			}
		}
	}


	/**
	 * @throws OrderException
	 */
	private function saveOrders(): void {
		$order = new Order();
		$order->setParty($this->party->getOwner());
		$order->setGame($this->game);
		$order->setTurn($this->round);
		$order->setOrders($this->content);
		$this->orderService->setContext($order);
		if (!$this->orderService->saveOrders()) {
			throw new OrderException('Die Befehle konnten nicht gespeichert werden.', 1);
		}
	}

	private function getCheckResult(): array {
		$engine = $this->engineService->get($this->game);
		$this->checkService->readRules($engine->getRulesFile());
		return $this->checkService->check($this->content);
	}

	private function getSimulationProblems(array $skip = []): ?array {
		$engine = $this->engineService->get($this->game);
		if ($engine->canSimulate($this->game, $this->round)) {
			$result     = [];
			$simulation = explode(PHP_EOL, $this->orderService->getSimulation());
			$unit       = null;
			foreach ($simulation as $line) {
				if ($line) {
					if ($line[0] === '[') {
						$type = $line[1];
						if ($type !== ' ') {
							if (!in_array($type, $skip)) {
								if ($unit) {
									$result[] = $unit;
									$unit     = null;
								}
								$result[] = $line;
							}
						}
					} else {
						$unit = $line;
					}
				}
			}
			return $result;
		}
		return null;
	}
}

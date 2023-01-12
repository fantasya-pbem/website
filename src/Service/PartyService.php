<?php
declare(strict_types = 1);
namespace App\Service;

use JetBrains\PhpStorm\Pure;

use App\Entity\Game;
use App\Entity\User;
use App\Exception\NoEngineException;
use App\Game\Engine;
use App\Game\Newbie;
use App\Game\Party;

readonly class PartyService
{
	#[Pure] public function __construct(private GameService $service, private EngineService $engineService) {
	}

	public function getAll(Game $game): array {
		return $this->engineService->get($game)->getAll($game);
	}

	/**
	 * Find a party by its Base-36 ID.
	 *
	 * @throws NoEngineException
	 */
	public function getById(string $id, Game $game): ?Party {
		return $this->engineService->get($game)->getById($id, $game);
	}

	/**
	 * Find a party in a game by its owner ID.
	 */
	public function getByOwner(string $owner, Game $game): ?Party {
		return $this->engineService->get($game)->getByOwner($owner, $game);
	}

	/**
	 * Get all parties of a User.
	 *
	 * @return array<int, array<Party>>
	 */
	public function getFor(User $user): array {
		$games   = $this->service->getAll();
		$parties = [];
		foreach ($games as $game) {
			$parties[$game->getId()] = $this->engineService->get($game)->getParties($user, $game);
		}
		return $parties;
	}

	/**
	 * Get parties in current Game of a User.
	 *
	 * @return array<Party>
	 */
	public function getCurrent(User $user): array {
		$game = $this->service->getCurrent();
		return $this->engineService->get($game)->getParties($user, $game);
	}

	/**
	 * Get all newbies of a User.
	 *
	 * @return array<int, array<Newbie>>
	 */
	public function getNewbies(User $user): array {
		$games   = $this->service->getAll();
		$newbies = [];
		foreach ($games as $game) {
			$newbies[$game->getId()] = $this->engineService->get($game)->getNewbies($user, $game);
		}
		return $newbies;
	}

	/**
	 * Check if a User has a Party in a Game.
	 */
	public function hasParty(User $user, Game $game): bool {
		$parties = $this->getFor($user);
		return !empty($parties[$game->getId()]);
	}

	/**
	 * Check if a User has a Newbie in a Game.
	 */
	public function hasNewbie(User $user, Game $game): bool {
		$newbies = $this->getNewbies($user);
		return !empty($newbies[$game->getId()]);
	}

	/**
	 * Check if a User has a Party or Newbie in a Game.
	 */
	public function hasAny(User $user, Game $game): bool {
		return $this->hasParty($user, $game) || $this->hasNewbie($user, $game);
	}

	/**
	 * Check if a User has a Party in a legacy game.
	 */
	public function hasLegacy(User $user): bool {
		foreach ($this->service->getAll() as $game) {
			if ($game->getEngine() === Engine::FANTASYA) {
				$parties = $this->engineService->get($game)->getParties($user, $game);
				if (!empty($parties)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Update eMail address of user's parties and newbies.
	 */
	public function update(User $user): void {
		$games = $this->service->getAll();
		foreach ($games as $game) {
			$this->engineService->get($game)->updateUser($user, $game);
		}
	}

	public function create(Newbie $newbie): void {
		$game = $this->service->getCurrent();
		$this->engineService->get($game)->create($newbie, $game);
	}

	public function delete(Newbie $newbie, Game $game): void {
		$this->engineService->get($game)->delete($newbie, $game);
	}
}

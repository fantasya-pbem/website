<?php
declare (strict_types = 1);
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

use App\Entity\Game;
use App\Entity\User;
use App\Exception\NoEngineException;
use App\Game\Engine;
use App\Game\Engine\Fantasya;
use App\Game\Newbie;
use App\Game\Party;

/**
 * A service for fetching parties.
 */
class PartyService
{
	/**
	 * @var array(string=>Engine)
	 */
	private array $engines;

	#[Pure] public function __construct(private GameService $service, EntityManagerInterface $manager) {
		$this->engines = [Engine::FANTASYA => new Fantasya($manager), Engine::LEMURIA  => null];//TODO
	}

	/**
	 * Find a party by its Base-36 ID.
	 *
	 * @throws NoEngineException
	 */
	public function getById(string $id, Game $game): ?Party {
		return $this->getEngine($game)->getById($id, $game);
	}

	/**
	 * Get all parties of a User.
	 */
	public function getFor(User $user): array {
		$games   = $this->service->getAll();
		$parties = [];
		foreach ($games as $game) {
			$parties[$game->getId()] = $this->getEngine($game)->getParties($user, $game);
		}
		return $parties;
	}

	/**
	 * Get parties in current Game of a User.
	 *
	 * @return Party[]
	 */
	public function getCurrent(User $user): array {
		$game = $this->service->getCurrent();
		return $this->getEngine($game)->getParties($user, $game);
	}

	/**
	 * Get all newbies of a User.
	 *
	 * @return Newbie[]
	 */
	public function getNewbies(User $user): array {
		$games   = $this->service->getAll();
		$newbies = [];
		foreach ($games as $game) {
			$newbies[$game->getId()] = $this->getEngine($game)->getNewbies($user, $game);
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
	 * Update eMail address of user's parties and newbies.
	 */
	public function update(User $user) {
		$games = $this->service->getAll();
		foreach ($games as $game) {
			$this->getEngine($game)->updateUser($user, $game);
		}
	}

	public function create(Newbie $newbie) {
		$game = $this->service->getCurrent();
		$this->getEngine($game)->create($newbie, $game);
	}

	public function delete(Newbie $newbie) {
		$game = $this->service->getCurrent();
		$this->getEngine($game)->delete($newbie, $game);
	}

	private function getEngine(Game $game): Engine {
		$engine = $this->engines[$game->getEngine()] ?? null;
		if ($engine instanceof Engine) {
			return $engine;
		}
		throw new NoEngineException($game->getEngine());
	}
}

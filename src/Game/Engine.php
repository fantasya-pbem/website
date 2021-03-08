<?php
declare(strict_types = 1);
namespace App\Game;

use App\Entity\Game;
use App\Entity\User;

interface Engine
{
	public const FANTASYA = 'fantasya';

	public const LEMURIA = 'lemuria';

	/**
	 * Get the last game round.
	 */
	public function getRound(Game $game): int;

	/**
	 * Get the last game round time.
	 */
	public function getLastZat(Game $game): \DateTime;

	/**
	 * Find a party in a game by its Base-36 ID.
	 */
	public function getById(string $id, Game $game): ?Party;

	/**
	 * Find a party in a game by its owner ID.
	 */
	public function getByOwner(string $owner, Game $game): ?Party;

	/**
	 * Find all parties of a user in a game.
	 *
	 * @return Party[]
	 */
	public function getParties(User $user, Game $game): array;

	/**
	 * Find all new parties of a user in a game.
	 *
	 * @return Newbie[]
	 */
	public function getNewbies(User $user, Game $game): array;

	/**
	 * Get game statistict.
	 */
	public function getStatistics(Game $game): Statistics;

	/**
	 * Update eMail address of a user's parties and newbies.
	 */
	public function updateUser(User $user, Game $game): void;

	/**
	 * Create a new party.
	 */
	public function create(Newbie $newbie, Game $game): void;

	/**
	 * Delete a new party.
	 */
	public function delete(Newbie $newbie, Game $game): void;
}

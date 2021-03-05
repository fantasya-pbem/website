<?php
declare (strict_types = 1);
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Game;
use App\Exception\NoEngineException;
use App\Game\Engine;
use App\Game\Engine\Fantasya;
use App\Game\Engine\Lemuria;
use App\Repository\AssignmentRepository;

class EngineService
{
	private static ?array $engines = null;

	public function __construct(private GameService $service, EntityManagerInterface $manager,
										AssignmentRepository $repository) {
		if (!self::$engines) {
			self::$engines = [
				Engine::FANTASYA => new Fantasya($manager), Engine::LEMURIA => new Lemuria($repository, $manager)
			];
		}
	}

	/**
	 * @throws NoEngineException
	 */
	public function get(Game $game): Engine {
		$engine = self::$engines[$game->getEngine()] ?? null;
		if ($engine instanceof Engine) {
			return $engine;
		}
		throw new NoEngineException($game->getEngine());
	}
}

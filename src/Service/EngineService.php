<?php
declare(strict_types = 1);
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Entity\Game;
use App\Exception\NoEngineException;
use App\Game\Engine;
use App\Game\Engine\Fantasya;
use App\Game\Engine\Lemuria;
use App\Repository\AssignmentRepository;

class EngineService
{
	/**
	 * @var array<string, Engine>
	 */
	private static ?array $engines = null;

	public function __construct(ContainerBagInterface $container, ManagerRegistry $managerRegistry,
								AssignmentRepository $repository) {
		if (!self::$engines) {
			self::$engines = [
				Engine::FANTASYA => new Fantasya($container, $managerRegistry),
				Engine::LEMURIA  => new Lemuria($container, $repository, $managerRegistry)
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

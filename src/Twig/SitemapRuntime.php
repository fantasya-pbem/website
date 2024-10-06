<?php
declare(strict_types = 1);
namespace App\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;

use App\Game\Turn;
use App\Repository\GameRepository;
use App\Service\EngineService;

readonly class SitemapRuntime implements RuntimeExtensionInterface
{
	protected const string GAME_ALIAS = 'lemuria';

	public function __construct(protected GameRepository $gameRepository, protected EngineService $engineService,
		                        protected EntityManagerInterface $entityManager
	) {
	}

	public function tableMod(string $table, string $column = 'created_at', string $orderBy = 'id'): string {
		$connection = $this->entityManager->getConnection();
		$result     = $connection->executeQuery(
			'SELECT `' . $column . '` FROM `' . $table . '` ORDER BY `' . $orderBy . '` DESC LIMIT 1'
		);
		$createdAt  = $result->fetchOne();
		return is_string($createdAt) ? $createdAt : '';
	}

	public function templateMod(string $template): string {
		$path      = __DIR__ . '/../../templates/' . $template . '.html.twig';
		$timestamp = file_exists($path) ? filemtime($path) : 0;
		return $timestamp > 0 ? date('Y-m-d', $timestamp) : '';
	}

	public function turnMod(string $gameAlias = self::GAME_ALIAS): string {
		$game = $this->gameRepository->findByAlias($gameAlias);
		$turn = new Turn($game, $this->engineService);
		return $turn->getLast()->format('Y-m-d');
	}
}

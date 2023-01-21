<?php
declare(strict_types = 1);
namespace App\Command\Check;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Command\AbstractCheckCommand;
use App\Game\Engine\Fantasya;
use App\Service\CheckService;

#[AsCommand('check:fantasya', 'Check an order file.')]
class FantasyaCommand extends AbstractCheckCommand
{
	public function __construct(ContainerBagInterface $config, CheckService $checkService,
		                        Fantasya $engine) {
		parent::__construct($config, $checkService);
		$this->rules = $engine->getRulesFile();
	}
}

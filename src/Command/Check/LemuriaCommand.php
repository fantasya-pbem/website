<?php
declare(strict_types = 1);
namespace App\Command\Check;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Command\AbstractCheckCommand;
use App\Game\Engine\Lemuria;
use App\Service\CheckService;

#[AsCommand('check:lemuria', 'Check an order file.')]
class LemuriaCommand extends AbstractCheckCommand
{
	public function __construct(ContainerBagInterface $config, CheckService $checkService,
		                        Lemuria $engine) {
		parent::__construct($config, $checkService);
		$this->rules = $engine->getRulesFile();
	}
}

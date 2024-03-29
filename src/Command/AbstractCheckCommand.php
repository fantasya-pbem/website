<?php
declare(strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Service\CheckService;

abstract class AbstractCheckCommand extends Command
{
	protected string $rules;

	public function __construct(protected readonly ContainerBagInterface $config, protected readonly CheckService $checkService) {
		parent::__construct();
	}

	protected function configure(): void {
		$this->setHelp('This command allows to check an order file.');
		$this->addArgument('orders', InputArgument::REQUIRED, 'Order file');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$orders = $input->getArgument('orders');
		$this->checkService->readRules($this->rules);

		$commands = file_get_contents($orders);
		$result   = $this->checkService->check($commands);
		if (empty($result)) {
			$output->writeln('Die Schreibweise der Befehle scheint in Ordnung zu sein.');
			return 0;
		}
		foreach ($result as $line) {
			$output->writeln($line);
		}
		return 1;
	}
}

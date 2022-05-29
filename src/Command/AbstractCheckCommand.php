<?php
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Exception\MailfilterException;
use App\Service\CheckService;

abstract class AbstractCheckCommand extends Command
{
	protected string $rules;

	public function __construct(protected ContainerBagInterface $config, protected CheckService $checkService) {
		parent::__construct();
	}

	protected function configure(): void {
		$this->setHelp('This command allows to check an order file.');
		$this->addArgument('orders', InputArgument::REQUIRED, 'Order file');
	}

	/**
	 * @throws MailfilterException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$orders = $input->getArgument('orders');
		$this->checkService->readRules($this->rules);

		$commands = file_get_contents($orders);
		$result   = $this->checkService->check($commands);
		if (empty($result)) {
			$output->writeln('Die Befehle scheinen in Ordnung zu sein.');
			return 0;
		}
		foreach ($result as $line) {
			$output->writeln($line);
		}
		return 1;
	}
}

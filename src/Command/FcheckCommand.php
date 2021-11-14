<?php
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Exception\MailfilterException;

class FcheckCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'fcheck:test';

	public function __construct(private ContainerBagInterface $config) {
		parent::__construct();
	}

	protected function configure(): void {
		$this->setDescription('Test Fcheck.');
		$this->setHelp('This command allows to check Fcheck templates.');

		$this->addArgument('orders', InputArgument::REQUIRED, 'Order file');
		$this->addArgument('game', InputArgument::REQUIRED, 'Game name (fantasya, lemuria)');
	}

	/**
	 * @throws MailfilterException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$command = $this->config->get('app.fcheck');
		$orders  = $input->getArgument('orders');
		$game    = $input->getArgument('game');
		$result  = tempnam('/tmp', 'fcheck');
		$command = str_replace('%input%', $orders, $command);
		$command = str_replace('%game%', $game, $command);
		$command = str_replace('%output%', $result, $command);

		$output->writeln($command);
		exec($command, $lines, $code);
		unlink($result);
		foreach ($lines as $line) {
			$output->writeln($line);
		}
		return $code;
	}
}

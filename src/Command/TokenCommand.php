<?php
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Security\Token;

class TokenCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'token:create';

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$token = new Token();
	}
}

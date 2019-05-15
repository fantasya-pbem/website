<?php
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Game\Party;
use App\Security\Token;

class DownloadTokenCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'download:token';

	/**
	 * Set description and help.
	 */
	protected function configure() {
		$this->setDescription('Generate a URL token for anonymous report download.');
		$this->setHelp('This command generates a token for a given eMail address and turn number.');

		$this->addArgument('game', InputArgument::REQUIRED, 'Game ID');
		$this->addArgument('party', InputArgument::REQUIRED, 'Party ID');
		$this->addArgument('email', InputArgument::REQUIRED, 'eMail address');
		$this->addArgument('turn', InputArgument::REQUIRED, 'Turn number');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$game  = (int)$input->getArgument('game');
		$party = $input->getArgument('party');
		$email = $input->getArgument('email');
		$turn  = (int)$input->getArgument('turn');

		$idPart = dechex(2 ** 24 * $game + Party::fromId($party));
		$token  = new Token();
		$token->setEmail($email)->setTurn($turn);

		$output->writeln($token . $idPart);
	}
}

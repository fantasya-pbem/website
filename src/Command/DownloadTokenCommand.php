<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Game\Party;
use App\Security\Token;

class DownloadTokenCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'download:token';

	private string $secret;

	public function __construct(ContainerBagInterface $config) {
		parent::__construct();
		$this->secret = $config->get('app.secret');
	}

	protected function configure(): void {
		$this->setDescription('Generate a URL token for anonymous report download.');
		$this->setHelp('This command generates a token for a given eMail address and turn number.');

		$this->addArgument('game', InputArgument::REQUIRED, 'Game ID');
		$this->addArgument('party', InputArgument::REQUIRED, 'Party ID');
		$this->addArgument('email', InputArgument::REQUIRED, 'eMail address');
		$this->addArgument('turn', InputArgument::REQUIRED, 'Turn number');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$game  = (int)$input->getArgument('game');
		$party = $input->getArgument('party');
		$email = $input->getArgument('email');
		$turn  = (int)$input->getArgument('turn');

		$idPart = dechex(2 ** 24 * $game + Party::fromId($party));
		$token  = new Token($this->secret);
		$token->setEmail($email)->setTurn($turn);

		$output->writeln($token . $idPart);
		return 0;
	}
}

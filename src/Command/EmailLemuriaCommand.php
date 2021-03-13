<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repository\AssignmentRepository;

class EmailLemuriaCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'email:lemuria';

	public function __construct(private AssignmentRepository $assignmentRepository) {
		parent::__construct();
	}

	protected function configure(): void {
		$this->setDescription('Get user email address for a given Lemuria party.');
		$this->setHelp('This command fetches the user email address for a given Lemuria party UUID.');

		$this->addArgument('party', InputArgument::REQUIRED, 'Party UUID');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$party = $input->getArgument('party');

		$assignment = $this->assignmentRepository->find($party);
		$email      = $assignment?->getUser()->getEmail();
		if ($email) {
			$output->writeln($email);
			return 0;
		}
		return 1;
	}
}

<?php
declare(strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Game;
use App\Game\Party;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Service\PartyService;

#[AsCommand('game:mail', 'Send game email to players.')]
class MailCommand extends Command
{
	private const string ENGINE = 'lemuria';

	private const string SUBJECT = 'SUBJECT:';

	private const string TEMPLATE_NAME = 'emails/game_mail.html.twig';

	private const string TEMPLATE = __DIR__ . '/../../templates/' . self::TEMPLATE_NAME;

	protected ?Game $game;

	protected string $subject;

	protected string $template;

	private OutputInterface $output;

	private bool $doSend;

	public function __construct(private readonly GameRepository $gameRepository, private readonly UserRepository $userRepository,
								private readonly MailService $mailService, private readonly PartyService $partyService,
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this->setHelp('Send an email from the game master account to all players.');

		$this->addArgument('game', InputArgument::REQUIRED, 'Game alias');
		$this->addArgument('template', InputArgument::REQUIRED, 'Path to email template');
		$this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not send email for real');
		$this->addOption('party', 'p', InputOption::VALUE_REQUIRED, 'Specify a base-36 party ID to send this report only');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$this->output = $output;

		$gameAlias  = $input->getArgument('game');
		$this->game = $this->gameRepository->findByAlias($gameAlias);
		if (!$this->game) {
			$output->writeln('Game ' . $gameAlias . ' does not exist.');
			return 1;
		}
		if ($this->game->getEngine() !== self::ENGINE) {
			$output->writeln('Game ' . $gameAlias . ' has wrong engine.');
			return 1;
		}
		$this->readTemplate($input->getArgument('template'));

		$partyId = $input->getOption('party');
		if ($partyId) {
			try {
				$party = $this->partyService->getById($partyId, $this->game);
				if (!$party || !$party->isPlayer()) {
					$output->writeln('Party ' . $partyId . ' is not a player faction.');
					return 1;
				}
				$parties = [$party];
			} catch (NotRegisteredException $e) {
				return $this->throwError($e);
			}
		} else {
			$parties = $this->partyService->getAll($this->game);
		}

		$this->doSend = !$input->getOption('dry-run');
		if (!$this->doSend) {
			$output->writeln('Dry run - no emails are sent out.', OutputInterface::VERBOSITY_DEBUG);
		}

		$result = 0;
		foreach ($parties as $party) {
			/** @var Party $party */
			if (!$party->isPlayer()) {
				$output->writeln('Skipping non-player party ' . $party->getName() . '.');
				continue;
			}
			if ($party->isRetired()) {
				$output->writeln('Skipping retired party ' . $party->getName() . '.');
				continue;
			}
			try {
				$this->send($party);
			} catch (\Throwable $e) {
				$result += $this->throwError($e);
			}
		}

		return $result;
	}

	protected function send(Party $party): void {
		$partyId   = $party->getId();
		$partyName = $party->getName() . ' [' . $partyId . ']';
		$this->output->writeln('Sending email to party ' . $partyName . '...', OutputInterface::VERBOSITY_DEBUG);

		$user = $this->userRepository->find($party->getUser());
		if (!$user) {
			throw new \RuntimeException('Error: Could not find user of party ' . $partyName . '.');
		}

		$mail = $this->mailService->withReport($user);
		$mail->subject($this->subject);
		$mail->textTemplate(self::TEMPLATE_NAME);
		$mail->context(['user' => $user, 'party' => $party]);

		if ($this->doSend) {
			$this->mailService->signAndSend($mail);
		}
	}

	protected function readTemplate(string $path): void {
		if (!is_file($path)) {
			throw new \RuntimeException('Could not read template file.');
		}
		$template = file_get_contents($path);
		if (!$template) {
			throw new \RuntimeException('Template file is empty.');
		}
		$lines = explode(PHP_EOL, $template);
		if (!str_starts_with($lines[0], self::SUBJECT)) {
			throw new \RuntimeException('Expected ' . self::SUBJECT . ' in first line of template.');
		}
		$this->subject = trim(substr($lines[0], strlen(self::SUBJECT)));
		if (count($lines) <= 1) {
			throw new \RuntimeException('No text in template.');
		}
		unset($lines[0]);
		$template = trim(implode(PHP_EOL, $lines));
		file_put_contents(self::TEMPLATE, $template);
	}

	protected function throwError(\Throwable $throwable): int {
		$this->output->writeln($throwable->getMessage());
		return 255;
	}
}

<?php
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Entity\Game;
use App\Entity\User;
use App\Game\Party;
use App\Game\Turn;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Security\DownloadToken;
use App\Service\EngineService;
use App\Service\MailService;
use App\Service\PartyService;

abstract class AbstractSendCommand extends Command
{
	protected ?Game $game;

	protected int $round;

	private DownloadToken $token;

	private OutputInterface $output;

	private bool $doSend;

	public function __construct(ContainerBagInterface $config, private GameRepository $gameRepository,
		                        private UserRepository $userRepository, private EngineService $engineService,
		                        private MailService $mailService, private PartyService $partyService
	) {
		parent::__construct();
		$this->token = new DownloadToken($config->get('app.secret'));
	}

	protected function configure(): void {
		$this->setHelp('After a turn has been evaluated, this command is called to send the reports by email.');

		$this->addArgument('game', InputArgument::REQUIRED, 'Game alias');
		$this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not send email for real');
		$this->addOption('party', 'p', InputOption::VALUE_REQUIRED, 'Specify a base-36 party ID to send this report only');
		$this->addOption('round', 'r', InputOption::VALUE_REQUIRED, 'Override round number (default: current)');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$this->output = $output;

		$gameAlias  = $input->getArgument('game');
		$this->game = $this->gameRepository->findByAlias($gameAlias);
		if (!$this->game) {
			$output->writeln('Game ' . $gameAlias . ' does not exist.');
			return 1;
		}
		if ($this->game->getEngine() !== $this->getEngine()) {
			$output->writeln('Game ' . $gameAlias . ' has wrong engine.');
			return 1;
		}

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

		$round = (int)$input->getOption('round');
		if ($round > 0) {
			$this->round = $round;
		} else {
			$turn        = new Turn($this->game, $this->engineService);
			$this->round = $turn->getRound();
			$output->writeln('Current round is ' . $this->round . '.', OutputInterface::VERBOSITY_DEBUG);
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
		$id        = Party::fromId($partyId);
		$partyName = $party->getName() . ' [' . $partyId . ']';
		$this->output->writeln('Sending email to party ' . $partyName . '...', OutputInterface::VERBOSITY_DEBUG);

		$user = $this->userRepository->find($party->getUser());
		if (!$user) {
			throw new \RuntimeException('Error: Could not find user of party ' . $partyName . '.');
		}
		$this->token->setGame($this->game->getId())->setParty($id)->setEmail($user->getEmail())->setTurn($this->round);

		$report = null;
		if ($user->hasFlag(User::FLAG_WITH_ATTACHMENT)) {
			$file   = $this->round . '-' . $partyId . '.zip';
			$report = realpath(__DIR__ . '/../../var/zip/' . $this->game->getAlias() . '/' . $this->round . '/' . $file);
			if (!$report || !is_file($report)) {
				throw new \RuntimeException('Report ' . $file . ' does not exist.');
			}
		}

		$mail = $this->mailService->withReport($user);
		$mail->subject($this->getSubject());
		$mail->textTemplate($this->getTemplate());
		$mail->context(['user' => $user, 'round' => $this->round, 'token' => $this->token, 'withReport' => (bool)$report]);
		if ($report) {
			$mail->attachFromPath($report);
			/** @noinspection PhpUndefinedVariableInspection */
			$this->output->writeln('Attaching report ' . $file . ' for party ' . $partyName . '.', OutputInterface::VERBOSITY_VERBOSE);
		} else {
			$this->output->writeln('Adding report link only for party ' . $partyName . '.', OutputInterface::VERBOSITY_VERBOSE);
		}

		if ($this->doSend) {
			$this->mailService->signAndSend($mail);
		}
	}

	protected function throwError(\Throwable $throwable): int {
		$this->output->writeln($throwable->getMessage());
		return 255;
	}

	abstract protected function getEngine(): string;

	abstract protected function getSubject(): string;

	abstract protected function getTemplate(): string;
}

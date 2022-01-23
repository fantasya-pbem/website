<?php
declare (strict_types = 1);
namespace App\Command\Send;

use Lemuria\Engine\Fantasya\Storage\NewcomerConfig;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Catalog;
use Lemuria\Model\Exception\NotRegisteredException;
use Lemuria\Model\Fantasya\Party as LemuriaParty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use App\Entity\Game;
use App\Entity\User;
use App\Game\Party;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Security\DownloadToken;
use App\Service\MailService;
use App\Service\PartyService;

class LemuriaCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'send:lemuria';

	protected Game $game;

	protected DownloadToken $token;

	private OutputInterface $output;

	public function __construct(ContainerBagInterface $config,
								GameRepository $gameRepository, private UserRepository $userRepository,
		                        private MailService $mailService, private PartyService $partyService
	) {
		parent::__construct();
		$this->game  = $gameRepository->findByAlias('lemuria');
		$this->token = new DownloadToken($config->get('app.secret'));
		$this->token->setGame($this->game->getId());
	}

	protected function configure(): void {
		$this->setDescription('Send game report emails to players.');
		$this->setHelp('After a turn has been evaluated, this command is called to send the reports by email.');

		$this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not send email for real');
		$this->addOption('party', 'p', InputOption::VALUE_REQUIRED, 'Specify a base-36 party ID to send this report only');
		$this->addOption('round', 'r', InputOption::VALUE_REQUIRED, 'Override round number (default: current)');
	}

	/**
	 * @throws MailfilterException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$this->output = $output;
		$config       = new NewcomerConfig(__DIR__ . '/../../../var/lemuria');
		Lemuria::init($config);
		Lemuria::load();

		$party = $input->getOption('party');
		if ($party) {
			try {
				$party = LemuriaParty::get(Id::fromId($party));
				if ($party->Type() !== LemuriaParty::PLAYER) {
					$output->writeln('Party ' . $party . ' is not a player faction.');
					return 1;
				}
				$parties = [$party];
			} catch (NotRegisteredException $e) {
				return $this->throwError($e);
			}
		} else {
			$parties = Lemuria::Catalog()->getAll(Catalog::PARTIES);
		}

		$current = Lemuria::Calendar()->Round();
		$round   = (int)$input->getOption('round');
		$round   = $round > 0 && $round <= $current ? $round : $current;

		$dryRun = $input->getOption('dry-run');
		if ($dryRun) {
			$output->writeln('Dry run - no emails are sent out.', OutputInterface::VERBOSITY_DEBUG);
		}

		$result = 0;
		foreach ($parties as $party) {
			/** @var LemuriaParty $party */
			if ($party->Type() !== LemuriaParty::PLAYER) {
				continue;
			}
			try {
				$this->send($party, $round, !$dryRun);
			} catch (\Throwable $e) {
				$result += $this->throwError($e);
			}
		}

		return $result;
	}

	private function send(LemuriaParty $party, int $round, bool $send): void {
		$this->output->writeln('Sending email to party ' . $party . '...', OutputInterface::VERBOSITY_DEBUG);

		$gameParty  = $this->partyService->getById((string)$party->Id(), $this->game);
		$user       = $gameParty ? $this->userRepository->find($gameParty->getUser()) : null;
		if (!$user) {
			throw new \RuntimeException('Error: Could not find user of party ' . $party . '.');
		}
		$this->token->setParty(Party::fromId($gameParty->getId()))->setEmail($user->getEmail())->setTurn($round);

		$report = null;
		if ($user->hasFlag(User::FLAG_WITH_ATTACHMENT)) {
			$file   = $round . '-' . $party->Id() . '.zip';
			$report = realpath(__DIR__ . '/../../../var/zip/lemuria/' . $round . '/' . $file);
			if (!$report || !is_file($report)) {
				throw new \RuntimeException('Report ' . $file . ' does not exist.');
			}
		}

		$mail = $this->mailService->withReport($user);
		$mail->subject('Lemuria AW ' . $round);
		$mail->textTemplate('emails/send_lemuria.html.twig');
		$mail->context(['user' => $user, 'round' => $round, 'token' => $this->token, 'withReport' => (bool)$report]);
		if ($report) {
			$mail->attachFromPath($report);
			$this->output->writeln('Attaching report ' . $file . ' for party ' . $party . '.', OutputInterface::VERBOSITY_VERBOSE);
		} else {
			$this->output->writeln('Adding report link only for party ' . $party . '.', OutputInterface::VERBOSITY_VERBOSE);
		}

		if ($send) {
			$this->mailService->signAndSend($mail);
		}
	}

	private function throwError(\Throwable $throwable): int {
		$this->output->writeln($throwable->getMessage());
		return 255;
	}
}

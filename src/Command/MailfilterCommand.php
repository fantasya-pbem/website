<?php
declare (strict_types = 1);
namespace App\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Data\Order;
use App\Entity\Game;
use App\Entity\User;
use App\Exception\MailfilterException;
use App\Game\Party;
use App\Game\Turn;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\OrderService;
use App\Service\PartyService;

class MailfilterCommand extends Command
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'mail:filter';

	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var GameRepository
	 */
	private $gameRepository;

	/**
	 * @var PartyService
	 */
	private $partyService;

	/**
	 * @var OrderService
	 */
	private $orderService;

	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * @var PasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Game
	 */
	private $game;

	/**
	 * @var Party
	 */
	private $party;

	/**
	 * @var int
	 */
	private $round;

	/**
	 * @var string[]
	 */
	private $header = [];

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @param UserRepository $userRepository
	 * @param GameRepository $gameRepository
	 * @param PartyService $partyService
	 * @param OrderService $orderService
	 * @param EntityManagerInterface $manager
	 * @param UserPasswordEncoderInterface $encoder
	 * @param \Swift_Mailer $mailer
	 */
	public function __construct(UserRepository $userRepository, GameRepository $gameRepository,
								PartyService $partyService, OrderService $orderService,
								EntityManagerInterface $manager,
								UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer) {
		parent::__construct();
		$this->userRepository = $userRepository;
		$this->gameRepository = $gameRepository;
		$this->partyService   = $partyService;
		$this->orderService   = $orderService;
		$this->manager        = $manager;
		$this->encoder        = $encoder;
		$this->mailer         = $mailer;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws \Exception
	 */
	public function run(InputInterface $input, OutputInterface $output) {
		try {
			return parent::run($input, $output);
		} catch (\RuntimeException $e) {
			$output->writeln('Fehler: Falscher Aufruf des Mailfilter-Skripts.');
			return 1;
		}
	}

	/**
	 * Set description and help.
	 */
	protected function configure() {
		$this->setDescription('Receive Fantasya orders via eMail.');
		$this->setHelp('This command is a Postfix mail filter that receives and saves Fantasya orders.');

		$this->addArgument('sender', InputArgument::REQUIRED, 'Mail sender');
		$this->addArgument('size', InputArgument::REQUIRED, 'Mail size');
		$this->addArgument('recipient', InputArgument::REQUIRED, 'Recipient address');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @throws MailfilterException
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$sender    = $input->getArgument('sender');
		$size      = (int)$input->getArgument('size');
		$recipient = $input->getArgument('recipient');

		$this->fetchMailContent($size);
		$this->fetchGame($recipient);
		$this->fetchUserParty($sender);
		$this->getRound();
		$this->saveOrders();

		$fcheck = $this->orderService->getFcheck();
		$this->sendAnswerMail($recipient, $fcheck);
	}

	/**
	 * @param int $size
	 * @throws MailfilterException
	 */
	private function fetchMailContent(int $size) {
		$file = @fopen('php://stdin', 'r');
		if (!$file) {
			throw new MailfilterException('Die E-Mail konnte nicht eingelesen werden.', 2);
		}

		$email = '';
		while (!@feof($file)) {
			$email .= @fread($file, 8192);
		}
		@fclose($file);

		$length = strlen($this->content);
		if ($length !== $size) {
			throw new MailfilterException('Die Größe der E-Mail ist ' . $length . ' Byte statt ' . $size . ' Byte.', 2);
		}

		// Header und Mailtext trennen:
		$email        = str_replace("\r\n", "\n", $email);
		$firstLinePos = strpos($email, "\n\n");
		if (!$firstLinePos) {
			throw new MailfilterException('Anfang der Befehle nicht gefunden.', 2);
		}
		$headers = trim(substr($email, 0, $firstLinePos));
		if (strlen($headers) <= 0) {
			throw new MailfilterException('Keine E-Mail-Header vorhanden.', 2);
		}
		$this->content = substr($email, $firstLinePos + 2);

		foreach (explode("\n", preg_replace('/\n[ \t]+/', ' ', $headers)) as $h) {
			if (preg_match("/^([A-Z][A-Za-z-]*):[ \t]+(.*)$/", $h, $matches) === 1) {
				$tag = $matches[1];
				if (!isset($this->header[$tag])) {
					$this->header[$tag] = array();
				}
				$this->header[$tag][] = rtrim($matches[2]);
			}
		}

		// E-Mail-Format validieren:
		$type = isset($this->header['Content-Type']) ? $this->header['Content-Type'] : array('');
		if (strpos($type[0], 'text/plain') !== 0) {
			throw new MailfilterException('Falsches E-Mail-Format: ' . $type[0], 2);
		}
		$charset   = 'UTF-8';
		$typeComma = strpos($type[0], ';');
		if ($typeComma > 0) {
			$charsetLine = strtolower(trim(substr($type[0], $typeComma + 1)));
			if (strpos($charsetLine, 'charset') === 0) {
				$equal   = strpos($charsetLine, '=');
				$charset = strtoupper(trim(substr($charsetLine, $equal + 1)));
			}
		}
		$encoding = isset($this->header['Content-Transfer-Encoding']) ?
			strtolower($this->header['Content-Transfer-Encoding'][0]) : 'quoted-printable';

		// E-Mail-Text decodieren:
		switch ($encoding) {
			case 'quoted-printable' :
				$this->content = quoted_printable_decode($this->content);
				break;
			case 'base64' :
				$this->content = base64_decode($this->content);
				break;
			default :
				// sollte sonst 8bit sein
		}

		// E-Mail-Text nach UTF-8 wandeln:
		if ($charset !== 'UTF-8') {
			$this->content = iconv($charset, 'UTF-8//TRANSLIT', $this->content);
			if ($this->content === false) {
				throw new MailfilterException('E-Mail mit Charset ' . $charset . ' wird nicht unterstützt.', 2);
			}
		}
		$this->content = trim($this->content);
		if (strlen($this->content) <= 0) {
			throw new MailfilterException('Leerer E-Mail-Text.', 2);
		}
	}

	/**
	 * @param string $recipient
	 * @throws MailfilterException
	 */
	private function fetchGame(string $recipient) {
		$atPos = strpos($recipient, '@fantasya-pbem.de');
		if ($atPos <= 0) {
			throw new MailfilterException('Empfängeradresse fehlerhaft.', 3);
		}
		$mailbox = substr($recipient, 0, $atPos);

		$alias = null;
		switch ($mailbox) {
			case 'befehle' :
				$alias = 'spiel';
				break;
			case 'beta' :
			case 'test' :
				$alias = 'beta';
				break;
			default :
				throw new MailfilterException('Unbekanntes Postfach: ' . $mailbox, 3);
		}

		$this->game = $this->gameRepository->findOneBy(['alias' => $alias]);
		if (!$this->game) {
			throw new MailfilterException('Postfachzuordnung fehlerhaft.', 1);
		}
	}

	/**
	 * @param string $sender
	 * @throws MailfilterException
	 */
	private function fetchUserParty(string $sender) {
		// Befehle extrahieren:
		$endOfLine = strpos($this->content, "\n");
		if (!$endOfLine) {
			throw new MailfilterException('Befehle bestehen nur aus einer Zeile.', 2);
		}
		$firstLine = substr($this->content, 0, $endOfLine);
		if (strlen($firstLine) <= 0) {
			throw new MailfilterException('Erste Befehlszeile ist leer.', 2);
		}
		if (!preg_match('/^([^ ]+)[ ]+([a-zA-Z0-9]+)[ ]+"([^"]*)"$/', $firstLine, $parts) || count($parts) < 4) {
			throw new MailfilterException('Fehler: Erste Befehlszeile fehlerhaft.', 2);
		}
		$clientGame = $parts[1]; //currently unused
		$party      = $parts[2];
		$password   = $parts[3];

		try {
			$this->party = $this->partyService->getById($party, $this->game);
		} catch (DBALException $e) {
			$message = 'Partei ' . $party . ' existiert nicht im Spiel ' . $this->game->getName() . '.';
			throw new MailfilterException($message, 4, $e);
		}

		$this->user = $this->userRepository->find($this->party->getUser());
		if (!$this->user) {
			throw new MailfilterException('User #' . $this->party->getUser() . ' nicht gefunden.', 1);
		}
		if (!$this->encoder->isPasswordValid($this->user, $password)) {
			throw new MailfilterException('Passwort falsch.', 4);
		}
		if ($sender !== $this->user->getEmail()) {
			throw new MailfilterException('Absenderadresse falsch.', 4);
		}
	}

	/**
	 * @throws MailfilterException
	 */
	private function getRound() {
		try {
			$current = new Turn($this->game, $this->manager->getConnection());
		} catch (DBALException $e) {
			throw new MailfilterException('Aktuelle Runde konnte nicht ermittelt werden.', 1, $e);
		}
		$this->round = $current->getRound();

		if (preg_match('/^RUNDE\h+([0-9]+)\s*$/im', $this->content, $matches)) {
			$maxRound = $this->round + 4;
			$round    = (int)$matches[1];
			if ($round > $this->round && $round <= $maxRound) {
				$this->round = $round;
			} else {
				throw new MailfilterException('RUNDE ' . $round . ' ist nicht gültig.', 4);
			}
		}
	}

	/**
	 * @throws MailfilterException
	 */
	private function saveOrders() {
		$order = new Order();
		$order->setParty($this->party->getOwner());
		$order->setGame($this->game->getAlias());
		$order->setTurn($this->round);
		$order->setOrders($this->content);
		$this->orderService->setContext($order);
		if (!$this->orderService->saveOrders()) {
			throw new MailfilterException('Befehle konnten nicht gespeichert werden.', 1);
		}
	}

	/**
	 * @param string $from
	 * @param string $fcheck
	 * @throws MailfilterException
	 */
	private function sendAnswerMail(string $from, string $fcheck) {
		$subject = isset($this->header['Subject']) ? 'Re: ' . $this->header['Subject'][0]
			                                       : 'Fantasya-Befehle sind angekommen';
		$body    = "Deine Befehle für Runde " . ($this->round + 1) . " sind angekommen.\n\n";
		if ($fcheck) {
			$body .= $fcheck . "\n\n";
		}

		$mail = new \Swift_Message();
		if (isset($this->header['Message-ID'])) {
			$mail->getHeaders()->addTextHeader('In-Reply-To', $this->header['Message-ID'][0]);
		}
		$mail->setFrom($from, 'Fantasya-Server');
		$mail->setReplyTo('admin@fantasya-pbem.de', 'Fantasya-Administrator');
		$mail->setTo($this->user->getEmail(), $this->user->getName());
		$mail->setSubject($subject);
		$mail->setBody($body);
		if ($this->mailer->send($mail) < 1) {
			throw new MailfilterException('Antwortmail konnte nicht gesendet werden.', 5);
		}
	}
}

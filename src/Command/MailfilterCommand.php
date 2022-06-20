<?php
declare (strict_types = 1);
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Exception\OrderException;
use App\Game\OrderTrait;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\EngineService;
use App\Service\MailService;
use App\Service\OrderService;
use App\Service\PartyService;

#[AsCommand('mail:filter', 'Receive Fantasya orders via eMail.')]
class MailfilterCommand extends Command
{
	use OrderTrait;

	/**
	 * @var string[]
	 */
	private array $header = [];

	public function __construct(private readonly UserRepository $userRepository, private readonly GameRepository $gameRepository,
								private readonly PartyService $partyService, private readonly OrderService $orderService,
								private readonly MailService $mailService, private readonly EngineService $engineService,
								private readonly UserPasswordHasherInterface $hasher) {
		parent::__construct();
		setlocale(LC_ALL, 'de_DE');
	}

	public function run(InputInterface $input, OutputInterface $output): int {
		try {
			return parent::run($input, $output);
		} catch (OrderException $e) {
			$output->writeln($e->getMessage());
			if ($e->getPrevious()) {
				$output->writeln($e->getPrevious()->getMessage());
			}
			return $e->getCode();
		} catch (\RuntimeException $e) {
			$output->writeln('Fehler: Falscher Aufruf des Mailfilter-Skripts.');
			$output->writeln((string)$e);
			return 1;
		}
	}

	protected function configure(): void {
		$this->setHelp('This command is a Postfix mail filter that receives and saves Fantasya orders.');
		$this->addArgument('sender', InputArgument::REQUIRED, 'Mail sender');
		$this->addArgument('size', InputArgument::REQUIRED, 'Mail size');
		$this->addArgument('recipient', InputArgument::REQUIRED, 'Recipient address');
	}

	/**
	 * @throws OrderException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$size      = (int)$input->getArgument('size');
		$recipient = $input->getArgument('recipient');

		$this->fetchMailContent($size);
		$this->fetchGame($recipient);
		$this->fetchUserParty();
		$this->getRound();
		$this->saveOrders();

		$check = $this->orderService->getCheck();
		if ($this->engineService->get($this->game)->canSimulate($this->game, $this->round)) {
			$simulation = $this->orderService->getSimulation();
		} else {
			$simulation = '';
		}

		$this->sendAnswerMail($recipient, $check, $simulation);
		return 0;
	}

	/**
	 * @throws OrderException
	 */
	private function fetchMailContent(int $size): void {
		$file = @fopen('php://stdin', 'r');
		if (!$file) {
			throw new OrderException('Die E-Mail konnte nicht eingelesen werden.', 2);
		}

		$email = '';
		while (!@feof($file)) {
			$email .= @fread($file, 8192);
		}
		@fclose($file);
		//file_put_contents(__DIR__ . '/../../var/log/email.log', date('c') . PHP_EOL . $email . PHP_EOL . PHP_EOL, FILE_APPEND);

		$length = strlen($email);
		/** @noinspection PhpStatementHasEmptyBodyInspection */
		if ($length !== $size) {
			// Postfix $size is not applicable to $length.
		}

		// Header und Mailtext trennen:
		$email        = str_replace("\r\n", "\n", $email);
		$firstLinePos = strpos($email, "\n\n");
		if (!$firstLinePos) {
			throw new OrderException('Anfang der Befehle nicht gefunden.', 2);
		}
		$headers = trim(substr($email, 0, $firstLinePos));
		if (strlen($headers) <= 0) {
			throw new OrderException('Keine E-Mail-Header vorhanden.', 2);
		}
		$this->content = substr($email, $firstLinePos + 2);

		foreach (explode("\n", preg_replace('/\n[ \t]+/', ' ', $headers)) as $h) {
			if (preg_match("/^([A-Z][A-Za-z-]*):[ \t]+(.*)$/", $h, $matches) === 1) {
				$tag = $matches[1];
				if (!isset($this->header[$tag])) {
					$this->header[$tag] = [];
				}
				$this->header[$tag][] = rtrim($matches[2]);
			}
		}

		// E-Mail-Format validieren:
		$contentType = explode(';', $this->header['Content-Type'][0] ?? '');
		$type        = strtolower(trim($contentType[0]));
		if ($type !== 'text/plain') {
			throw new OrderException('Falsches E-Mail-Format: ' . $type, 2);
		}
		$charset = 'UTF-8';
		for ($i = 1; $i < count($contentType); $i++) {
			$charsetPart = explode('=', $contentType[$i]);
			if (count($charsetPart) === 2 && strtolower(trim($charsetPart[0])) === 'charset') {
				$charset = strtoupper(trim($charsetPart[1]));
				break;
			}
		}
		$encoding = strtolower($this->header['Content-Transfer-Encoding'][0] ?? 'quoted-printable');

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
				throw new OrderException('E-Mail mit Charset ' . $charset . ' wird nicht unterstützt.', 2);
			}
		}
		$this->content = trim($this->content);
		if (strlen($this->content) <= 0) {
			throw new OrderException('Leerer E-Mail-Text.', 2);
		}
	}

	/**
	 * @throws OrderException
	 * @noinspection PhpConditionAlreadyCheckedInspection
	 */
	private function fetchGame(string $recipient): void {
		$atPos = strpos($recipient, '@fantasya-pbem.de');
		if ($atPos <= 0) {
			throw new OrderException('Die Empfängeradresse ist fehlerhaft.', 3);
		}
		$mailbox = substr($recipient, 0, $atPos);

		try {
			$alias = match ($mailbox) {
				'befehle'      => 'spiel',
				'beta', 'test' => 'beta',
				'lemuria'      => 'lemuria'
			};
		} catch (\UnhandledMatchError $e) {
			throw new OrderException('Das Postfach ' . $mailbox . ' ist unbekannt.', 3, $e);
		}

		$this->game = $this->gameRepository->findOneBy(['alias' => $alias]);
		if (!$this->game) {
			throw new OrderException('Die Postfachzuordnung ist fehlerhaft.', 1);
		}
	}

	/**
	 * @throws OrderException
	 */
	private function sendAnswerMail(string $from, string $check, string $simulation): void {
		$subject = isset($this->header['Subject'][0]) ? 'Re: ' . $this->header['Subject'][0]
			                                          : 'Fantasya-Befehle sind angekommen';
		$body    = "Deine Befehle für Runde " . ($this->round + 1) . " sind angekommen.\n";
		if ($check) {
			$body .= "\n" . rtrim($check) . "\n";
		}
		if ($simulation) {
			$body .= "\n" . $simulation;
		}
		$body .= "\n";

		$mail = $this->mailService->fromServer($from, $this->user);
		if (isset($this->header['Message-ID'])) {
			$messageId = trim($this->header['Message-ID'][0], '< >');
			$mail->getHeaders()->addTextHeader('In-Reply-To', $messageId)->addTextHeader('References', $messageId);
		}
		$mail->subject($subject);
		$mail->text($body);
		try {
			$this->mailService->signAndSend($mail);
		} catch (\Throwable $e) {
			throw new OrderException('Die Antwortmail konnte nicht gesendet werden.', 5, $e);
		}
	}
}

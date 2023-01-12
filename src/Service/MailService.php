<?php
declare(strict_types = 1);
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Crypto\SMimeSigner;
use Symfony\Component\Mime\Email;

use App\Entity\User;

class MailService
{
	private string $userAgent;

	private string $serverName;

	private Address $admin;

	private Address $gameMaster;

	private string $cert;

	private string $key;

	private string $password;

	public function __construct(ContainerBagInterface $config, private readonly MailerInterface $mailer,
		                        private readonly BodyRendererInterface $bodyRenderer) {
		$this->userAgent  = $config->get('app.mail.user.agent');
		$this->serverName = $config->get('app.mail.server.name');
		$this->admin      = new Address($config->get('app.mail.admin.address'), $config->get('app.mail.admin.name'));
		$this->gameMaster = new Address($config->get('app.mail.game.address'), $config->get('app.mail.game.name'));
		$certsDir         = realpath(__DIR__ . '/../../var/certs') . DIRECTORY_SEPARATOR;
		$this->cert       = $certsDir . $config->get('app.mail.cert');
		$this->key        = $certsDir . $config->get('app.mail.key');
		$this->password   = $config->get('app.mail.key.password');
	}

	public function create(?Email $mail = null): Email {
		if (!$mail) {
			$mail = new Email();
		}
		$mail->getHeaders()->addTextHeader('User-Agent', $this->userAgent);
		return $mail;
	}

	public function fromAdmin(?User $to = null): Email {
		$mail = $this->create()->from($this->admin);
		if ($to) {
			$mail->to(new Address($to->getEmail(), $to->getName()));
		}
		return $mail;
	}

	public function fromServer(string $from, ?User $to = null): Email {
		$mail = $this->create()->from(new Address($from, $this->serverName));
		$mail->replyTo($this->admin);
		if ($to) {
			$mail->to(new Address($to->getEmail(), $to->getName()));
		}
		return $mail;
	}

	public function toGameMaster(): Email {
		return $this->create()->from($this->admin)->to($this->gameMaster);
	}

	public function withReport(?User $to = null): TemplatedEmail {
		$mail = new TemplatedEmail();
		$this->create($mail)->from($this->gameMaster);
		if ($to) {
			$mail->to(new Address($to->getEmail(), $to->getName()));
		}
		return $mail;
	}

	public function send(Email $mail): void {
		$this->mailer->send($mail);
	}

	public function signAndSend(Email $mail): void {
		if ($mail instanceof TemplatedEmail) {
			$this->bodyRenderer->render($mail);
		}
		$signer     = new SMimeSigner($this->cert, $this->key, $this->password);
		$signedMail = $signer->sign($mail);
		$this->mailer->send($signedMail);
	}
}

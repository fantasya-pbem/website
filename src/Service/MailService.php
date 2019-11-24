<?php
declare (strict_types = 1);
namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Crypto\SMimeSigner;
use Symfony\Component\Mime\Email;

/**
 * A service for sending Fantasya mails.
 */
class MailService
{
	/**
	 * @var MailerInterface
	 */
	private $mailer;

	/**
	 * @var string
	 */
	private $userAgent;

	/**
	 * @var string
	 */
	private $serverName;

	/**
	 * @var Address
	 */
	private $admin;

	/**
	 * @var Address
	 */
	private $gameMaster;

	/**
	 * @var string
	 */
	private $cert;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @param MailerInterface $mailer
	 * @param ContainerBagInterface $config
	 */
	public function __construct(MailerInterface $mailer, ContainerBagInterface $config) {
		$this->mailer     = $mailer;
		$this->userAgent  = $config->get('app.mail.user.agent');
		$this->serverName = $config->get('app.mail.server.name');
		$this->admin      = new Address($config->get('app.mail.admin.address'), $config->get('app.mail.admin.name'));
		$this->gameMaster = new Address($config->get('app.mail.game.address'), $config->get('app.mail.game.name'));
		$certsDir         = realpath(__DIR__ . '/../../var/certs') . DIRECTORY_SEPARATOR;
		$this->cert       = $certsDir . $config->get('app.mail.cert');
		$this->key        = $certsDir . $config->get('app.mail.key');
		$this->password   = $config->get('app.mail.key.password');
	}

	/**
	 * @return Email
	 */
	public function create(): Email {
		$mail = new Email();
		$mail->getHeaders()->addTextHeader('User-Agent', $this->userAgent);
		return $mail;
	}

	/**
	 * @param User|null $to
	 * @return Email
	 */
	public function fromAdmin(?User $to = null): Email {
		$mail = $this->create()->from($this->admin);
		if ($to) {
			$mail->to(new Address($to->getEmail(), $to->getName()));
		}
		return $mail;
	}

	/**
	 * @param string $from
	 * @param User|null $to
	 * @return Email
	 */
	public function fromServer(string $from, ?User $to = null): Email {
		$mail = $this->create()->from(new Address($from, $this->serverName));
		$mail->replyTo($this->admin);
		if ($to) {
			$mail->to(new Address($to->getEmail(), $to->getName()));
		}
		return $mail;
	}

	/**
	 * @return Email
	 */
	public function toGameMaster(): Email {
		return $this->create()->from($this->admin)->to($this->gameMaster);
	}

	/**
	 * @param Email $mail
	 * @throws \Throwable
	 */
	public function send(Email $mail): void {
		$this->mailer->send($mail);
	}

	/**
	 * @param Email $mail
	 * @throws \Throwable
	 */
	public function signAndSend(Email $mail): void {
		$signer     = new SMimeSigner($this->cert, $this->key, $this->password);
		$signedMail = $signer->sign($mail);
		$this->mailer->send($signedMail);
	}
}

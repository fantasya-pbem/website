<?php
declare (strict_types = 1);
namespace App\Command\Send;

use App\Command\AbstractSendCommand;

class LemuriaCommand extends AbstractSendCommand
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'send:lemuria';

	protected function getSubject(): string {
		return 'Lemuria AW ' . $this->round;
	}

	protected function getTemplate(): string {
		return 'emails/send_lemuria.html.twig';
	}
}

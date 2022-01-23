<?php
declare (strict_types = 1);
namespace App\Command\Send;

use App\Command\AbstractSendCommand;

class FantasyaCommand extends AbstractSendCommand
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'send:fantasya';

	protected function getSubject(): string {
		return 'Fantasya AW ' . $this->round;
	}

	protected function getTemplate(): string {
		return 'emails/send_fantasya.html.twig';
	}
}

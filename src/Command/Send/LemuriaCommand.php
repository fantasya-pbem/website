<?php
declare (strict_types = 1);
namespace App\Command\Send;

use JetBrains\PhpStorm\Pure;

use App\Command\AbstractSendCommand;

class LemuriaCommand extends AbstractSendCommand
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'send:lemuria';

	protected function getEngine(): string {
		return 'lemuria';
	}

	#[Pure] protected function getSubject(): string {
		return $this->game->getName() . ' AW ' . $this->round;	}

	protected function getTemplate(): string {
		return 'emails/send_lemuria.html.twig';
	}
}

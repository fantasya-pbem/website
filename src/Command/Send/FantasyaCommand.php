<?php
declare (strict_types = 1);
namespace App\Command\Send;

use JetBrains\PhpStorm\Pure;

use App\Command\AbstractSendCommand;

class FantasyaCommand extends AbstractSendCommand
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'send:fantasya';

	protected function getEngine(): string {
		return 'fantasya';
	}

	#[Pure] protected function getSubject(): string {
		return $this->game->getName() . ' AW ' . $this->round;
	}

	protected function getTemplate(): string {
		return 'emails/send_fantasya.html.twig';
	}
}

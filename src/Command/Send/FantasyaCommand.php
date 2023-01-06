<?php
declare(strict_types = 1);
namespace App\Command\Send;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Attribute\AsCommand;

use App\Command\AbstractSendCommand;

#[AsCommand('send:fantasya', 'Send game report emails to players.')]
class FantasyaCommand extends AbstractSendCommand
{
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

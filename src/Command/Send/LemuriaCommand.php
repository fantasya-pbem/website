<?php
declare(strict_types = 1);
namespace App\Command\Send;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Attribute\AsCommand;

use App\Command\AbstractSendCommand;

#[AsCommand('send:lemuria', 'Send game report emails to players.')]
class LemuriaCommand extends AbstractSendCommand
{
	protected function getEngine(): string {
		return 'lemuria';
	}

	#[Pure] protected function getSubject(): string {
		return $this->game->getName() . ' AW ' . $this->round;	}

	protected function getTemplate(): string {
		return 'emails/send_lemuria.html.twig';
	}
}

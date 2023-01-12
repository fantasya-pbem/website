<?php
declare(strict_types = 1);
namespace App\Command\Check;

use Symfony\Component\Console\Attribute\AsCommand;

use App\Command\AbstractCheckCommand;

#[AsCommand('check:lemuria', 'Check an order file.')]
class LemuriaCommand extends AbstractCheckCommand
{
	protected string $rules = __DIR__ . '/../../../var/check/lemuria.tpl';
}

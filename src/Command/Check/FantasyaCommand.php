<?php
declare(strict_types = 1);
namespace App\Command\Check;

use Symfony\Component\Console\Attribute\AsCommand;

use App\Command\AbstractCheckCommand;

#[AsCommand('check:fantasya', 'Check an order file.')]
class FantasyaCommand extends AbstractCheckCommand
{
	protected string $rules = __DIR__ . '/../../../var/check/fantasya.tpl';
}

<?php
declare (strict_types = 1);
namespace App\Command\Check;

use App\Command\AbstractCheckCommand;

class LemuriaCommand extends AbstractCheckCommand
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'check:lemuria';

	protected string $rules = __DIR__ . '/../../../var/check/lemuria.tpl';
}

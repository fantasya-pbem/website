<?php
declare (strict_types = 1);
namespace App\Command\Check;

use App\Command\AbstractCheckCommand;

class FantasyaCommand extends AbstractCheckCommand
{
	/**
	 * @var string
	 */
	protected static $defaultName = 'check:fantasya';

	protected string $rules = __DIR__ . '/../../../var/check/fantasya.tpl';
}

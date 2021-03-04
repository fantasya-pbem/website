<?php
declare(strict_types = 1);
namespace App\Game\Engine;

use JetBrains\PhpStorm\Pure;
use Lemuria\Test\TestConfig;

class LemuriaConfig extends TestConfig
{
	#[Pure] public function getPathToLog(): string {
		return realpath(__DIR__ . '/../../../var/log') . DIRECTORY_SEPARATOR . 'lemuria.log';
	}
}

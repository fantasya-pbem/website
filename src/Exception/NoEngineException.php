<?php
declare(strict_types = 1);
namespace App\Exception;

use JetBrains\PhpStorm\Pure;

class NoEngineException extends \RuntimeException
{
	#[Pure] public function __construct(string $engine) {
		parent::__construct('Invalid engine: ' . $engine);
	}
}

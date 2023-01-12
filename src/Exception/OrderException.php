<?php
declare(strict_types = 1);
namespace App\Exception;

use JetBrains\PhpStorm\Pure;

/**
 * Exception for mail filter command.
 */
class OrderException extends \RuntimeException
{
	#[Pure] public function __construct(string $message, int $code, ?\Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}

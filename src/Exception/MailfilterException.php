<?php
declare (strict_types = 1);
namespace App\Exception;

use Throwable;

/**
 * Exception for mail filter command.
 *
 * @package App\Exception
 */
class MailfilterException extends \RuntimeException
{
	/**
	 * @param string $message
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(string $message, int $code, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}

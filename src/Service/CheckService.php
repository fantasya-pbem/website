<?php
declare (strict_types = 1);
namespace App\Service;

class CheckService
{
	protected ?array $rules = null;

	public function check(string $commands): array {
		$result = [];
		$i      = 0;
		foreach (explode(PHP_EOL, $commands) as $command) {
			$i++;
			$command = trim($command);
			if ($command) {
				if (!$this->isValid($command)) {
					$result[] = 'Zeile ' . $i . ": '" . $command . "' ist kein gültiger Befehl.";
				}
			}
		}
		return $result;
	}

	public function isValid(string $command): bool {
		if (!$this->rules) {
			throw new \RuntimeException('You have to set the rules file first.');
		}

		$comment = strpos($command, ';');
		if ($comment !== false) {
			$command = substr($command, 0, $comment);
		}
		$command = mb_strtolower(trim($command));
		if (empty($command)) {
			return true;
		}

		foreach ($this->rules as $pattern) {
			if (preg_match($pattern, $command) === 1) {
				return true;
			}
		}
		return false;
	}

	public function readRules(string $path): void {
		$file = @fopen($path, 'r');
		if ($file) {
			while (!feof($file)) {
				$line = fgets($file);
				if ($line) {
					$line = trim($line);
					if ($line) {
						$this->rules[] = '#' . $line . '#';
					}
				}
			}
			@fclose($file);
		}
	}
}

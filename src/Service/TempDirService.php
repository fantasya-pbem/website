<?php
declare(strict_types = 1);
namespace App\Service;

class TempDirService
{
	private const string TMP_DIR = __DIR__ . '/../../var/temp';

	private static ?string $tmpDir = null;

	private static array $directories = [];

	public function __construct() {
		if (!self::$tmpDir) {
			self::$tmpDir = (string)realpath(self::TMP_DIR);
		}
	}

	public function create(string $prefix = ''): string {
		$tries = 5;
		do {
			$path = tempnam(self::$tmpDir, $prefix);
		} while (!$path && --$tries);
		if (!$path) {
			throw new \RuntimeException('Could not create a temp dir after five tries.');
		}
		if (!@unlink($path) || !mkdir($path)) {
			throw new \RuntimeException('Could not create temp dir ' . basename($path) . '.');
		}

		self::$directories[] = $path;
		return $path;
	}

	public function clean(): void {
		foreach (self::$directories as $path) {
			$this->delete($path);
		}
	}

	private function delete(string $path): void {
		if (is_file($path)) {
			unlink($path);
			return;
		}
		foreach (glob($path . DIRECTORY_SEPARATOR . '*') as $file) {
			$this->delete($file);
		}
		if (is_dir($path)) {
			rmdir($path);
		}
	}
}

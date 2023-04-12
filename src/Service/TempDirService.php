<?php
declare(strict_types = 1);
namespace App\Service;

class TempDirService
{
	private const TMP_DIR = __DIR__ . '/../../var/temp';

	private static ?string $tmpDir = null;

	private static array $directories = [];

	public function __construct() {
		if (!self::$tmpDir) {
			$tmpDir = realpath(self::TMP_DIR);
			if (!$tmpDir || !is_dir($tmpDir)) {
				if (!mkdir(self::TMP_DIR, 0750)) {
					throw new \RuntimeException('Could not create temp dir.');
				}
				self::$tmpDir = realpath(self::TMP_DIR);
			} else {
				self::$tmpDir = $tmpDir;
			}
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
		foreach (glob($path . DIRECTORY_SEPARATOR . '*') as $path) {
			$this->delete($path);
		}
		rmdir($path);
	}
}

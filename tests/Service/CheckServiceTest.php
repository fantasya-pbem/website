<?php
declare(strict_types = 1);
namespace App\Tests\Service;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use SATHub\PHPUnit\Base;

use App\Service\CheckService;

class CheckServiceTest extends Base
{
	protected const LINES = [
		'EINHEIT 123',
		'@MACHEN Pferde',
		'KEINBEFEHL',
		'; Kommentar',
		'// Kommentar',
		'BOTSCHAFT e12 Das hier ist eine\\',
		'lange Zeile',
		'WEITERMACHEN',
		'ENDE'
	];

	protected const VALID = [0, 1, 3, 4, 5, 8];

	protected const INVALID = [2, 7];

	#[Test]
	public function construct(): CheckService {
		$service = new CheckService();

		$this->assertNotNull($service);

		return $service;
	}

	#[Test, Depends('construct')]
	public function readRules(CheckService $service): CheckService {
		$service->readRules(__DIR__ . '/../check/test.tpl');

		$this->assertNotNull($service);

		return $service;
	}

	#[Test, Depends('readRules')]
	public function isValid(CheckService $service): void {
		$this->assertTrue($service->isValid(self::LINES[self::VALID[0]]));
		$this->assertFalse($service->isValid(self::LINES[self::INVALID[0]]));
	}

	#[Test, Depends('readRules')]
	public function check(CheckService $service): void {
		$result = $service->check(implode(PHP_EOL, self::LINES));

		$this->assertArray($result, 2, 'string');
		$this->assertArrayKey($result, 0, "Zeile 3: 'KEINBEFEHL' ist kein gültiger Befehl.");
		$this->assertArrayKey($result, 1, "Zeile 8: 'WEITERMACHEN' ist kein gültiger Befehl.");
	}
}

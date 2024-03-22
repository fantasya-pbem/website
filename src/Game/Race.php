<?php
declare(strict_types = 1);
namespace App\Game;

use JetBrains\PhpStorm\Pure;

class Race implements \Stringable
{
	public const string AQUAN = 'Aquaner';

	public const string DWARF = 'Zwerg';

	public const string ELF = 'Elf';

	public const string HALFLING = 'Halbling';

	public const string HUMAN = 'Mensch';

	public const string ORC = 'Ork';

	public const string TROLL = 'Troll';

	public const string MONSTER = 'Monster';

	/**
	 * @type array<string, string>
	 */
	protected const array FANTASYA = [
		self::AQUAN => 'Aquan', self::ELF => 'Elf', self::HALFLING => 'Halfling', self::HUMAN => 'Human',
		self::ORC   => 'Orc', self::TROLL => 'Troll', self::DWARF  => 'Dwarf', self::MONSTER  => 'Monster'
	];

	/**
	 * @type array<string, string>
	 */
	protected const array LEMURIA = [
		'Aquan' => self::AQUAN, 'Dwarf' => self::DWARF, 'Elf' => self::ELF, 'Halfling' => self::HALFLING,
		'Human' => self::HUMAN, 'Orc' => self::ORC, 'Troll' => self::TROLL
	];

	/**
	 * @return array<string>
	 */
	#[Pure] public static function all(): array {
		return array_keys(self::FANTASYA);
	}

	public static function lemuria(string $name): self {
		return new self(self::LEMURIA[$name] ?? self::MONSTER);
	}

	public function __construct(private readonly string $name) {
		if (!isset(self::FANTASYA[$name])) {
			throw new \RuntimeException('Invalid race: ' . $name);
		}
	}

	public function toLemuria(): string {
		return self::FANTASYA[$this->name];
	}

	public function __toString(): string {
		return $this->name;
	}
}

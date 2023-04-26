<?php
declare(strict_types = 1);
namespace App\Data;

class Reports implements \ArrayAccess
{
	public final const HTML = 'html';

	public final const TEXT = 'text';

	public final const MAGELLAN = 'magellan';

	protected const EXTENSIONS = ['cr' => self::MAGELLAN, 'html' => self::HTML, 'txt' => self::TEXT];

	private const ALL = [self::HTML, self::TEXT, self::MAGELLAN];

	public bool $html;

	public bool $text;

	public bool $magellan;

	public function __construct(Flags $flags) {
		$this->html     = $flags->hasFlag(Flag::HtmlReport);
		$this->text     = $flags->hasFlag(Flag::TextReport);
		$this->magellan = $flags->hasFlag(Flag::MagellanReport);
		if ($this->isClear()) {
			$this->all();
		}
	}

	/**
	 * @param string $offset
	 */
	public function offsetExists(mixed $offset): bool {
		return in_array($offset, self::ALL);
	}

	/**
	 * @param string $offset
	 */
	public function offsetGet(mixed $offset): bool {
		return $this->$offset;
	}

	/**
	 * @param string $offset
	 * @param bool $value
	 */
	public function offsetSet(mixed $offset, mixed $value): void {
		$this->$offset = $value;
	}

	/**
	 * @param string $offset
	 */
	public function offsetUnset(mixed $offset): void {
		$this->$offset = false;
	}

	public function isClear(): bool {
		return !($this->html || $this->text || $this->magellan);
	}

	public function all(): Reports {
		$this->html     = true;
		$this->text     = true;
		$this->magellan = true;
		return $this;
	}

	public function clear(): Reports {
		$this->html     = false;
		$this->text     = false;
		$this->magellan = false;
		return $this;
	}

	public function byExtension(string $extension): bool {
		if (!isset(self::EXTENSIONS[$extension])) {
			return false;
		}
		$format = self::EXTENSIONS[$extension];
		return $this->$format;
	}

	public function allowAdditionalFiles(): bool {
		return $this->html || $this->text;
	}

	public function setToFlags(Flags $flags): Flags {
		$flags->setFlag(Flag::HtmlReport, $this->html);
		$flags->setFlag(Flag::TextReport, $this->text);
		$flags->setFlag(Flag::MagellanReport, $this->magellan);
		return $flags;
	}
}

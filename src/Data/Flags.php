<?php
declare(strict_types = 1);
namespace App\Data;

final class Flags
{
	public function __construct(private int $flags = 0) {
	}

	public function get(): int {
		return $this->flags;
	}

	public function hasFlag(Flag $flag): bool {
		return ($this->flags & $flag->value) === $flag->value;
	}

	public function setFlag(Flag $flag, bool $set = true): self {
		if ($set) {
			$this->flags |= $flag->value;
		} else {
			$this->flags &= Flag::All->value - $flag->value;
		}
		return $this;
	}
}

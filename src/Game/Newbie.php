<?php
declare (strict_types = 1);
namespace App\Game;

/**
 * A helper class for newbies.
 */
class Newbie
{
	/**
	 * @var array
	 */
	private $properties;

	/**
	 * @param array $properties
	 */
	public function __construct(array $properties) {
		$this->properties = $properties;
	}

	/**
	 * @return string
	 */
	public function getRace(): string {
		return $this->properties['rasse'];
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->properties['name'];
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->properties['description'];
	}

	/**
	 * @return int
	 */
	public function getWood(): int {
		return (int)$this->properties['holz'];
	}

	/**
	 * @return int
	 */
	public function getStone(): int {
		return (int)$this->properties['steine'];
	}

	/**
	 * @return int
	 */
	public function getIron(): int {
		return (int)$this->properties['eisen'];
	}
}

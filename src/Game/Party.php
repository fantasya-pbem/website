<?php
declare (strict_types = 1);
namespace App\Game;

/**
 * A helper class for parties.
 */
class Party
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
	public function getId(): string {
		return $this->properties['id'];
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
		return $this->properties['beschreibung'];
	}

	/**
	 * @return int
	 */
	public function getOwner(): int {
		return (int)$this->properties['owner_id'];
	}

	/**
	 * @return int
	 */
	public function getUser(): int {
		return (int)$this->properties['user_id'];
	}
}

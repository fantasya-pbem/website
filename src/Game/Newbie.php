<?php
declare (strict_types = 1);
namespace App\Game;

use App\Data\Newbie as NewbieData;
use App\Entity\User;

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
	 * @param NewbieData $data
	 * @return Newbie
	 */
	public static function fromData(NewbieData $data): self {
		return new self([
			'name'        => $data->getName(),
			'description' => $data->getDescription(),
			'rasse'       => $data->getRace(),
			'holz'        => $data->getWood(),
			'steine'      => $data->getStone(),
			'eisen'       => $data->getIron(),
			'tarnung'     => '',
			'insel'       => 0,
			'password'    => ''
		]);
	}

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

	/**
	 * @return int|null
	 */
	public function getUserId(): ?int {
		return $this->properties['user_id'] ?? null;
	}

	/**
	 * @return array
	 */
	public function getProperties(): array {
		return $this->properties;
	}

	/**
	 * @param User $user
	 * @return self
	 */
	public function setUser(User $user): self {
		$this->properties['email']   = $user->getEmail();
		$this->properties['user_id'] = $user->getId();
		return $this;
	}
}

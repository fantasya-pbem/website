<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MythRepository")
 */
class Myth
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private ?int $id = null;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private string $myth;

	public function getId(): ?int {
		return $this->id;
	}

	public function getMyth(): string {
		return $this->myth;
	}

	public function setMyth(string $myth): self {
		$this->myth = $myth;
		return $this;
	}
}

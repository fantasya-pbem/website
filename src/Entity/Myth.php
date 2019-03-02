<?php
declare (strict_types = 1);
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
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $myth;

	/**
	 * @return int|null
	 */
    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return string|null
	 */
    public function getMyth(): ?string
    {
        return $this->myth;
    }

	/**
	 * @param string $myth
	 * @return Myth
	 */
    public function setMyth(string $myth): self
    {
        $this->myth = $myth;
        return $this;
    }
}

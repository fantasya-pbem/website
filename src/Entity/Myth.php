<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

use App\Repository\MythRepository;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: MythRepository::class)]
class Myth
{
	#[Column]
	#[GeneratedValue]
	#[Id]
	private ?int $id = null;

	#[Column]
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

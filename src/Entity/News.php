<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

use App\Repository\NewsRepository;

#[Entity(repositoryClass: NewsRepository::class)]
class News
{
	#[Column]
	#[GeneratedValue]
	#[Id]
	private ?int $id = null;

	#[Column(unique: true)]
	private ?\DateTime $created_at = null;

	#[Column]
	private string $title = '';

	#[Column(type: 'text')]
	private string $content = '';

	public function getId(): ?int {
		return $this->id;
	}

	public function getCreatedAt(): ?\DateTime {
		return $this->created_at;
	}

	public function setCreatedAt(\DateTime $createdAt): self {
		$this->created_at = $createdAt;
		return $this;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function setTitle(string $title): self {
		$this->title = $title;
		return $this;
	}

	public function getContent(): string {
		return str_replace('<br />', '', $this->content);
	}

	public function setContent(string $content): self {
		$this->content = nl2br($content);
		return $this;
	}
}

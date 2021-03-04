<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 */
class News
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private ?int $id = null;

	/**
	 * @ORM\Column(type="date", unique=true)
	 */
	private ?\DateTimeInterface $created_at = null;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private string $title = '';

	/**
	 * @ORM\Column(type="text")
	 */
	private string $content = '';

	public function getId(): ?int {
		return $this->id;
	}

	public function getCreatedAt(): ?\DateTimeInterface {
		return $this->created_at;
	}

	public function setCreatedAt(\DateTimeInterface $createdAt): self {
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

<?php
declare (strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 */
class News {

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 *
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="date", unique=true)
	 *
	 * @var \DateTimeInterface
	 */
	private $created_at;

	/**
	 * @ORM\Column(type="string", length=255)
	 *
	 * @var string
	 */
	private $title;

	/**
	 * @ORM\Column(type="text")
	 *
	 * @var string
	 */
	private $content;

	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * @return \DateTimeInterface|null
	 */
	public function getCreatedAt(): ?\DateTimeInterface {
		return $this->created_at;
	}

	/**
	 * @param \DateTimeInterface $createdAt
	 * @return News
	 */
	public function setCreatedAt(\DateTimeInterface $createdAt): self {
		$this->created_at = $createdAt;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getTitle(): ?string {
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return News
	 */
	public function setTitle(string $title): self {
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getContent(): ?string {
		return $this->content;
	}

	/**
	 * @param string $content
	 * @return News
	 */
	public function setContent(string $content): self {
		$this->content = $content;
		return $this;
	}
}

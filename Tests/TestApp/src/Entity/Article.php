<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Attribute as QAG;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Article
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    #[QAG\Field(label: 'Date of creation', position: 1)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    private ?Category $category = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[QAG\Field(help: 'The name of the Article.', position: 0)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $published = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}

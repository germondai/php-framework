<?php

declare(strict_types=1);

namespace Api\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medias')]
class Media extends Base
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $alt = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $source = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $path;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private int $size;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    private User $user;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): Media
    {
        $this->title = $title;
        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): Media
    {
        $this->alt = $alt;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): Media
    {
        $this->description = $description;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): Media
    {
        $this->source = $source;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Media
    {
        $this->name = $name;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Media
    {
        $this->path = $path;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Media
    {
        $this->type = $type;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): Media
    {
        $this->size = $size;
        return $this;
    }

    // User
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Media
    {
        $this->user = $user;
        return $this;
    }
}

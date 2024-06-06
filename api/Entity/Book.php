<?php

declare(strict_types=1);

namespace Api\Entity;

use Api\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'books')]
class Book extends BaseEntity
{
    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text', columnDefinition: 'TEXT')]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private User $user;

    public function __construct(string $title, string $content)
    {
        $this->setTitle($title);
        $this->setContent($content);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Book
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Book
    {
        $this->content = $content;
        return $this;
    }

    // User
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Book
    {
        $this->user = $user;
        return $this;
    }
}
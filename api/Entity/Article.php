<?php

declare(strict_types=1);

namespace Api\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
class Article extends Base
{
    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text', length: 65535)]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private User $user;

    public function __construct(string $title = null, string $content = null)
    {
        parent::__construct();
        $title ? $this->setTitle($title) : '';
        $content ? $this->setContent($content) : '';
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Article
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Article
    {
        $this->content = $content;
        return $this;
    }

    // User
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Article
    {
        $this->user = $user;
        return $this;
    }
}
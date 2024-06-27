<?php

declare(strict_types=1);

namespace App\Entity;

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
    private User $author;

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

    // Author
    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): Article
    {
        $this->author = $author;
        return $this;
    }
}
<?php

declare(strict_types=1);

namespace Api\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'events')]
class Event extends Base
{
    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text', length: 65535)]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private User $user;

    public function __construct(string $title, string $content)
    {
        parent::__construct();
        $this->setTitle($title);
        $this->setContent($content);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Event
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Event
    {
        $this->content = $content;
        return $this;
    }

    // User
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Event
    {
        $this->user = $user;
        return $this;
    }
}
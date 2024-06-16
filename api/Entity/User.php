<?php

declare(strict_types=1);

namespace Api\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User extends Base
{
    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $surname;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'user')]
    private Collection $articles;

    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'user')]
    private Collection $books;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'user')]
    private Collection $events;

    public function __construct()
    {
        parent::__construct();
        $this->articles = new ArrayCollection();
        $this->books = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): User
    {
        $this->surname = $surname;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    // Articles
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): User
    {
        $article->setUser($this);

        $this->articles->add($article);

        return $this;
    }

    public function removeArticle(Article $article): User
    {
        $this->articles->removeElement($article);
        return $this;
    }

    // Books
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): User
    {
        $book->setUser($this);

        $this->books->add($book);

        return $this;
    }

    public function removeBook(Book $book): User
    {
        $this->books->removeElement($book);
        return $this;
    }

    // Events
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): User
    {
        $event->setUser($this);

        $this->events->add($event);

        return $this;
    }

    public function removeEvent(Event $event): User
    {
        $this->events->removeElement($event);
        return $this;
    }
}
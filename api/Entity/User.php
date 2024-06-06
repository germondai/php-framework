<?php

declare(strict_types=1);

namespace Api\Entity;

use Api\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User extends BaseEntity
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

    public function __construct(string $name, string $surname, string $email, string $password)
    {
        $this->articles = new ArrayCollection();
        $this->books = new ArrayCollection();
        $this->setName($name);
        $this->setSurname($surname);
        $this->setEmail($email);
        $this->setPassword($password);
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
        $this->password = $password;
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
}

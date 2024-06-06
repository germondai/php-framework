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
    #[ORM\Column(type: 'string', length: 256)]
    private string $name;

    #[ORM\Column(type: 'string', length: 256)]
    private string $surname;

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 256)]
    private string $password;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'user')]
    private Collection $articles;

    public function __construct(string $name, string $surname, string $email, string $password)
    {
        $this->articles = new ArrayCollection();
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
}
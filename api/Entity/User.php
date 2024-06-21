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

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author')]
    private Collection $articles;

    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'author')]
    private Collection $medias;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->medias = new ArrayCollection();
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
        $article->setAuthor($this);

        $this->articles->add($article);

        return $this;
    }

    public function removeArticle(Article $article): User
    {
        $this->articles->removeElement($article);
        return $this;
    }

    // Medias
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): User
    {
        $media->setAuthor($this);

        $this->medias->add($media);

        return $this;
    }

    public function removeMedia(Media $media): User
    {
        $this->medias->removeElement($media);
        return $this;
    }
}
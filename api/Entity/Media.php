<?php

declare(strict_types=1);

namespace Api\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medias')]
class Media extends Base
{
    #[ORM\Column(type: 'string')]
    private ?string $title;

    #[ORM\Column(type: 'string')]
    private ?string $alt;

    #[ORM\Column(type: 'string')]
    private ?string $description;

    #[ORM\Column(type: 'string')]
    private ?string $source;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $path;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private string $size;

    #[ORM\ManyToOne(inversedBy: 'medias')]
    private User $user;

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
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Interface\Controller;
use Doctrine\ORM\EntityManager;
use Nette\Database\Explorer;
use Utils\Database;
use Utils\Doctrine;
use Utils\Helpers\Helper;

abstract class Base implements Controller
{
    protected Explorer $e;
    protected EntityManager $em;
    protected array $request;

    public function __construct()
    {
        $this->e = Database::explore();
        $this->em = Doctrine::getEntityManager();
        $this->request = $_SERVER['REQUEST'] ?? Helper::getRequest();
    }
}
<?php

declare(strict_types=1);

namespace App\Controller\Client;

use App\Controller\Base;
use App\Interface\Controller;
use Utils\Helper;

class Front extends Base implements Controller
{
    public function run(): void
    {
        $view = !empty($this->request) ? $this->request : 'index';
        include Helper::getBasePath() . 'src/includes/header.php';
        include Helper::getBasePath() . "app/View/{$view}.php";
        include Helper::getBasePath() . 'src/includes/footer.php';
    }
}

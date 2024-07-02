<?php

declare(strict_types=1);

namespace App\Controller\Client;

use App\Controller\Client;
use Utils\Helpers\PageHelper;

class Error extends Client
{
    public function render404()
    {
        PageHelper::setTitle('404 Not Found');
        $this->template->error = 404;
    }
}

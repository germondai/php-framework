<?php

declare(strict_types=1);

namespace App\Controller\Client;

use App\Controller\Client;

class Index extends Client
{
    public function renderIndex()
    {
        $this->template->greeting = 'Hello from Cotroller!';
    }

    public function renderAbout()
    {
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Client;

use App\Controller\Client;
use Utils\Helpers\PageHelper;

class Profile extends Client
{
    public function renderIndex()
    {
        PageHelper::setTitle('Profile');
        $this->template->user = 'Name Surname (from Controller)';
    }
}

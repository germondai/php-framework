<?php

declare(strict_types=1);

namespace App\Model\Users;

use App\Controller\Api\Entity;

class Admin extends Entity
{
    public function actionGet()
    {
        return [
            'message' => 'You tried to get your first Admin User',
            'data' => $this->params
        ];
    }
}
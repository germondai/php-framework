<?php

declare(strict_types=1);

namespace App\Model\Users;

use App\Controller\Api\Model;

class Admin extends Model
{
    public function actionGet()
    {
        return [
            'message' => 'You tried to get your first Admin User',
            'data' => $this->params
        ];
    }
}
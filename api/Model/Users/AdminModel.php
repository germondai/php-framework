<?php

declare(strict_types=1);

namespace Api\Model\Users;

use Api\Controller\Api;

class Admin extends Api
{
    public function actionGet()
    {
        return [
            'message' => 'You tried to get your first Admin User',
            'data' => $this->params
        ];
    }
}
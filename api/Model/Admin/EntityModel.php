<?php

declare(strict_types=1);

namespace Api\Model\Admin;

use Api\ApiController;

class EntityModel extends ApiController
{
    public function actionGetAll()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();

        return [
            'entities' => [
                'article' => 'Články',
                'book' => 'Knihy',
                'event' => 'Kurzy',
                'user' => 'Uživatelé'
            ]
        ];
    }
}
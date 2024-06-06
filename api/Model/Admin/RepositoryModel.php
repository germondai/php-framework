<?php

declare(strict_types=1);

namespace Api\Model\Admin;

use Api\ApiController;

class RepositoryModel extends ApiController
{
    public function actionGetAllRepositories()
    {
        $this->allowMethods(['GET']);
        $this->requireHeaders(['Authorization']);
        $user = $this->verifyJWT();

        return [
            'repositories' => [
                'article' => 'Články',
                'book' => 'Knihy',
                'event' => 'Kurzy',
                'user' => 'Uživatelé'
            ]
        ];
    }
}
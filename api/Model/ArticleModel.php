<?php

declare(strict_types=1);

namespace Api\Model;

use Api\ApiController;

class ArticleModel extends ApiController
{
    public function action()
    {
        $this->allowMethods(['GET']);

        return 'This is default action';
    }

    public function actionInsert()
    {
        $this->allowMethods(['POST']);

        if ($this->params) {
            $this->respond(
                [
                    'message' => 'You tried to insert your first Article',
                    'data' => $this->params
                ]
            );
        } else {
            $this->throwError(400);
        }
    }
}

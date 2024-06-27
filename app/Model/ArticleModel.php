<?php

declare(strict_types=1);

namespace App\Model;

use App\Controller\Api\Entity;
use App\Entity\Article;

class ArticleModel extends Entity
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

    public function actionGetAll()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();

        $articles = $this->em->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getArrayResult();

        return ['articles' => $articles];
    }
}

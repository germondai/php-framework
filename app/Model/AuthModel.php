<?php

declare(strict_types=1);

namespace App\Model;

use App\Controller\Api\Model;
use App\Entity\User;
use Utils\Helpers\Helper;
use Utils\Token;

class AuthModel extends Model
{
    public function action()
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();

        return [
            'user' => $user['user']
        ];
    }

    public function actionLogin()
    {
        $this->allowMethods(['POST']);
        $this->requireParams(['email', 'password']);

        $user = $this->em->getRepository(User::class)
            ->createQueryBuilder('u')
            ->select('u.id, u.name, u.surname, u.email, u.password')
            ->where('u.email = :email')
            ->setParameter('email', $this->params['email'])
            ->getQuery()->getOneOrNullResult();

        if (
            $user
            && password_verify($this->params['password'], $user['password'])
        ) {
            unset($user['password']);

            $api = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . Helper::link('');

            if (!empty($_ENV['MODE']) && $_ENV['MODE'] === 'api')
                $api = rtrim($api, '/');
            else
                $api .= 'api';

            return [
                'user' => $user,
                'token' => Token::generate([
                    'api' => $api,
                    'user' => $user
                ]),
            ];
        } else {
            $this->throwError(401);
        }
    }
}
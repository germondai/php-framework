<?php

declare(strict_types=1);

namespace Api\Model;

use Api\ApiController;
use Api\Entity\User;
use Utils\Token;

class AuthModel extends ApiController
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
            ->select('u.name, u.surname, u.email, u.password')
            ->where('u.email = :email')
            ->setParameter('email', $this->params['email'])
            ->getQuery()->getOneOrNullResult();

        if (
            $user
            && password_verify($this->params['password'], $user['password'])
        ) {
            unset($user['password']);

            return [
                'user' => $user,
                'token' => Token::generate(['user' => $user]),
            ];
        } else {
            $this->throwError(401);
        }
    }
}
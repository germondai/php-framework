<?php

declare(strict_types=1);

namespace Api\Model;

use Api\ApiController;
use Utils\Token;

class AuthModel extends ApiController
{
    private array $user = [
        'name' => 'Joe',
        'surname' => 'Doe',
        'email' => 'imejl',
    ];

    public function action()
    {
        $this->allowMethods(['GET']);
        $this->requireHeaders(['Authorization']);
        $this->verifyJWT();

        return
            $this->user
        ;
    }

    public function actionLogin()
    {
        $this->allowMethods(['POST']);
        $this->requireParams(['email', 'password']);

        if (
            $this->params['email'] === 'imejl'
            && $this->params['password'] === 'pass'
        ) {
            return [
                'user' => $this->user,
                'token' => Token::generate($this->user),
            ];
        } else {
            $this->throwError(401);
        }
    }
}
<?php

declare(strict_types=1);

namespace Api\Controller;

use Utils\Helper;

class Entity extends Api
{
    public function run(): void
    {
        dump($this->method);
        $route = str_replace(Helper::getLinkPath(), '', $_SERVER['REDIRECT_URL']);
        $params = explode('/', $route);

        $entityId = $params[0];
        $id = $params[1];
        dump($entityId);
        dump($id);
        dump($this->params);
        die;
    }

    private function schema(string $entityId)
    {
        $this->allowMethods(['GET']);
        $user = $this->verifyJWT();
    }

    private function get(string $entityId, int $id = null)
    {
        $this->allowMethods(['GET']);
    }

    private function post(string $entityId)
    {
        $this->allowMethods(['POST']);
        $user = $this->verifyJWT();
    }

    private function put(string $entityId, int $id)
    {
        $this->allowMethods(['PUT']);
        $user = $this->verifyJWT();
    }

    private function patch(string $entityId, int $id)
    {
        $this->allowMethods(['PATCH']);
        $user = $this->verifyJWT();
    }

    private function delete(string $entityId, int $id)
    {
        $this->allowMethods(['DELETE']);
        $user = $this->verifyJWT();
    }
}

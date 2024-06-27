<?php

declare(strict_types=1);

namespace App;

use App\Controller\Api\Entity;
use Utils\Helper;

class Bootstrap
{
    public function boot(): void
    {
        # set json and cors headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
        header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With");
        header('Content-Type: application/json; charset=utf-8');

        # preflight error fix
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
            die(200);

        $api = new Entity;
        $api->run();
        die;

        // TODO: !!!
        $request = Helper::getRequest();
        $parts = explode('/', $request);
        $reqMode = !empty($parts[0]) ? $parts[0] : false;

        $modes = ['api', 'client'];
        $prefMode = $_ENV['MODE'] ?? 'client';
        $a = array_search($reqMode, $modes);
        $isValidMode = $a === false ? -1 : $a;
        $mode = $modes[$isValidMode] ?? $prefMode;

        $controller = $parts[$isValidMode !== -1 ? 1 : 0] ?? false;
        $controller = empty($controller) ? 'index' : $controller;

        dump($parts);

        if ($mode === 'api') {
            # set json and cors headers
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
            header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With");
            header('Content-Type: application/json; charset=utf-8');

            # preflight error fix
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
                die(200);
        }

        $class = 'App\\Controller\\' . ucfirst($mode) . '\\' . ucfirst($controller);
        dump($class);
        if (class_exists($class)) {
            $contr = new $class;
            $contr->run();
        }

        dumpe($mode);
    }
}
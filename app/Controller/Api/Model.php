<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Base;

class Model extends Base
{
    public function run(): void
    {
        $solved = $this->solveRequest();
        $class = $solved['class'];
        $method = $solved['method'];

        if (class_exists($class)) {
            $model = new $class();

            if (method_exists($model, $method)) {
                $result = $model->$method();

                // fallback if return, no $this->respond()
                $result
                    ? $this->respond($result)
                    : $this->throwError(404);
            } else
                $this->throwError(404, 'Method not found');
        } else
            $this->throwError(404, 'Model not found');
    }

    protected function solveRequest(): array
    {
        $req = $this->request;

        $method = 'action' . ucfirst(array_pop($req) ?? '');
        $classParts = array_splice($req, -1, 1);


        if (!$classParts)
            $this->throwError(400, 'No model specified');

        $model = ucfirst($classParts[0]) . 'Model';
        $namespace = 'App\Model\\' . (!empty($req) ? implode('\\', array_map('ucfirst', $req)) . '\\' : '');
        $class = $namespace . $model;

        return [
            'class' => $class,
            'method' => $method,
        ];
    }
}

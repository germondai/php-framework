<?php

declare(strict_types=1);

namespace App\Controller;

use App\Interface\Controller;
use Doctrine\ORM\EntityManager;
use Nette\Database\Explorer;
use Utils\Database;
use Utils\Doctrine;
use Utils\Helper;
use Utils\Token;

abstract class Base implements Controller
{
    protected Explorer $e;
    protected EntityManager $em;
    protected array $params;
    protected array $headers;
    protected string $method;
    protected string $request;
    protected array $action;
    protected array $statuses = [
        400 => "Bad Request",
        401 => "Unauthorized",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        409 => "Conflict",
        410 => "Gone",
        422 => "Unprocessable Entity",
        429 => "Too Many Requests",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported"
    ];

    public function __construct()
    {
        $this->e = Database::explore();
        $this->em = Doctrine::getEntityManager();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setParams();
        $this->setHeaders();
        $this->setRequest();
    }

    private function setParams()
    {
        $requestData = json_decode(file_get_contents('php://input') ?? '', true) ?? [];
        $params = array_merge($requestData, $_POST, $_GET) ?? [];

        foreach ($params as $key => $val)
            if (in_array($val, [null, '', 'null', 'undefined', []]))
                unset($params[$key]);

        $this->params = $params;
    }

    private function setHeaders()
    {
        $this->headers = getallheaders();
        if (!empty($this->headers['authorization'])) {
            $this->headers['Authorization'] = $this->headers['authorization'];
            unset($this->headers['authorization']);
        }
    }

    private function setRequest()
    {
        $linkPath = Helper::getLinkPath();
        $request = str_replace($linkPath, '', $_SERVER['REDIRECT_URL']);
        if ($_SERVER['REDIRECT_URL'] === str_replace('public/', '', $linkPath))
            $request = '';

        $this->request = $request;
    }

    protected function solveRequest(): void
    {
        $req = $this->request;

        if (str_contains($req, '/')) {
            $requestParts = explode('/', $req);

            if (empty($requestParts[0]) || $requestParts[0] === 'model')
                unset($requestParts[0]);

            $method = 'action' . ucfirst(array_pop($requestParts));
            $classParts = array_splice($requestParts, -1, 1);

            if ($classParts) {
                $model = ucfirst($classParts[0]) . 'Model';
                $namespace = 'App\Model\\' . (!empty($requestParts) ? implode('\\', array_map('ucfirst', $requestParts)) . '\\' : '');
                $class = $namespace . $model;

                $this->action = [
                    'class' => $class,
                    'method' => $method,
                ];

                return;
            }
        }

        $this->throwError(400, 'No model specified');
    }

    public function callModel(): void
    {
        $this->solveRequest();

        $class = $this->action['class'];
        $method = $this->action['method'];

        if (class_exists($class)) {
            $model = new $class();

            if (method_exists($model, $method)) {
                $result = $model->$method();

                // fallback if return, no $this->respond()
                $result
                    ? $this->respond($result)
                    : $this->throwError(404);
            } else {
                $this->throwError(404, 'Method not found');
            }
        } else {
            $this->throwError(404, 'Model not found');
        }
    }

    protected function respond(array|string $response, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode(!is_array($response) ? ['data' => $response] : $response, 1);
        die();
    }

    protected function throwError(int $code = 400, array|string $error = null): void
    {
        $this->respond(
            [
                'error' =>
                    $error
                    ?? $this->statuses[$code]
                    ?? 'Something Went Wrong!'
            ],
            $code
        );
    }

    protected function allowMethods(array $allowedMethods): void
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
            $this->throwError(405);
        }
    }

    protected function requireHeaders(array $rHs): void
    {
        $this->requireValues($this->headers, $rHs);
    }

    protected function requireParams(array $rPs): void
    {
        $this->requireValues($this->params, $rPs);
    }

    private function requireValues(array $values, array $requiredValues, int $code = 400)
    {
        foreach ($requiredValues as $requiredValue) {
            if (empty($values[$requiredValue])) {
                $this->throwError($code);
            }
        }
    }

    protected function verifyJWT(bool $die = true): array|false
    {
        $this->requireHeaders(['Authorization']);

        $auth = $this->headers['Authorization'] ?? false;

        if ($auth)
            $token = Token::verify($auth);

        if (!empty($token))
            return $token;

        return $die ? $this->throwError(401) : false;
    }
}

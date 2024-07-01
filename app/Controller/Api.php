<?php

declare(strict_types=1);

namespace App\Controller;

use Utils\Token;

abstract class Api extends Base
{
    protected array $params;
    protected array $headers;
    protected string $method;
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
        parent::__construct();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setParams();
        $this->setHeaders();
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
        if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods))
            $this->throwError(405);
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
        foreach ($requiredValues as $requiredValue)
            if (empty($values[$requiredValue]))
                $this->throwError($code);
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

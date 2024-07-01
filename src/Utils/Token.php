<?php

declare(strict_types=1);

namespace Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Utils\Helpers\Helper;

class Token
{
    private static string $secret;
    private static string $alg;

    private static function init()
    {
        self::$secret = Helper::getEnv('JWT_SECRET', true);
        self::$alg = Helper::getEnv('JWT_ALG', true);
    }

    public static function generate(array $payload, int $exp = null, int $iat = null, int $nbf = null)
    {
        self::init();

        $time = time();
        $payload = array_merge(
            $payload,
            [
                'exp' => $exp ? $time + $exp : $time + 60 * 60,
                'iat' => $iat ? $time + $iat : $time,
                'nbf' => $nbf ? $time + $nbf : $time
            ]
        );

        return JWT::encode($payload, self::$secret, self::$alg);
    }

    public static function verify(string $token): array|false
    {
        self::init();

        if (str_contains($token, 'Bearer '))
            $token = str_replace('Bearer ', '', $token);

        try {
            return (array) JWT::decode($token, new Key(self::$secret, self::$alg));
        } catch (\Exception $e) {
            return false;
        }
    }
}

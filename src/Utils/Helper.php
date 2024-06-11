<?php

declare(strict_types=1);

namespace Utils;

class Helper
{
    private static string $basePath;
    private static string $linkPath;

    public static function setPaths(string $basePath, string $linkPath): void
    {
        self::$basePath = $basePath;
        self::$linkPath = $linkPath;
    }

    public static function getBasePath(): string
    {
        return self::$basePath;
    }

    public static function getLinkPath(): string
    {
        return self::$linkPath;
    }

    public static function isDev(): bool
    {
        $address = $_SERVER['SERVER_ADDR'] ?? true;
        return ($address == '127.0.0.1' || $address == '::1');
    }

    public static function formatLink(string $link): string
    {
        return str_starts_with($link, 'https://') ? $link : self::$linkPath . $link;
    }

    public static function getEnv(string $env, bool $die = false)
    {
        $envName = $env;
        $env = $_ENV[$env] ?? false;

        if ($env)
            return $env;

        trigger_error('Add valid "' . $envName . '" to .env!');

        if ($die)
            die();
    }

    public static function snakeToCamel($string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }
}
<?php

declare(strict_types=1);

namespace Utils;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Utils\Helpers\Helper;

class Doctrine
{
    private static Configuration $config;
    private static Connection $connection;

    public static function connect(array $params = null): void
    {
        # Config for EntityManager
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [Helper::getBasePath() . "app/Entity"],
            isDevMode: true,
        );
        $config->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));

        self::$config = $config;

        # Database Connection for EntityManager
        self::$connection = DriverManager::getConnection($params ?? [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'user' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? '',
            'dbname' => $_ENV['DB_NAME'],
            'driver' => $_ENV['DB_DRIV'] ?? 'pdo_mysql',
        ], self::$config);
    }

    public static function getEntityManager(): EntityManager
    {
        if (empty(self::$connection) && empty($config))
            self::connect();

        return new EntityManager(self::$connection, self::$config);
    }
}

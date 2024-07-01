<?php

declare(strict_types=1);

namespace Utils;

use Nette\Caching\Storages\FileStorage;
use Nette\Database\Connection;
use Nette\Database\Structure;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Database\Explorer;
use Utils\Helpers\Helper;

class Database
{
    private static Explorer $explorer;

    public static function connect(
        string $host = null,
        string $user = null,
        string $pass = null,
        string $ndsn = null,
        string $dbname = null
    ): void {
        # Create temp folder for storage
        $storagePath = Helper::getBasePath() . "temp";
        if (!is_dir($storagePath)) {
            mkdir($storagePath);
        }

        # Setup Credentials
        $host = $host ?? $_ENV['DB_HOST'] ?? 'localhost';
        $user = $user ?? $_ENV['DB_USER'] ?? 'root';
        $pass = $pass ?? $_ENV['DB_PASS'] ?? '';
        $ndsn = $ndsn ?? $_ENV['DB_NDSN'] ?? 'mysql';
        $dbname = $dbname ?? $_ENV['DB_NAME'];

        # Format DSN
        $dsn = $ndsn . ':host=' . $host . ';dbname=' . $dbname;

        # Connection
        $storage = new FileStorage($storagePath);
        $connection = new Connection($dsn, $user, $pass);
        $structure = new Structure($connection, $storage);
        $conventions = new DiscoveredConventions($structure);

        # Create Database Explorer
        self::$explorer = new Explorer($connection, $structure, $conventions, $storage);
    }

    public static function explore(): Explorer
    {
        if (empty(self::$explorer))
            self::connect();

        return self::$explorer;
    }
}

<?php

declare(strict_types=1);

# imports
use Utils\Helper;
use Utils\Database;
use Utils\Doctrine;

# paths
$basePath = realpath(__DIR__ . "/../") . "/";
$linkPath = dirname($_SERVER["PHP_SELF"]) . "/";

# autoload
try {
    require_once $basePath . "vendor/autoload.php";
} catch (\Throwable $th) {
    trigger_error('Install Composer Dependencies!');
}

# setup helper
Helper::setPaths($basePath, $linkPath);

# warnings
if (Helper::isDev()) {
    # enable
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    # disable
    error_reporting(0);
}

# session
if (session_status() == PHP_SESSION_NONE)
    session_start();

# .env
try {
    (Dotenv\Dotenv::createImmutable($basePath))->load();
} catch (\Throwable $th) {
    trigger_error('No .env detected!');
}

# database
try {
    Database::connect();
    $explorer = Database::explore();
} catch (\Throwable $th) {
    trigger_error('Database Connection Failed!');
}

# database
try {
    Doctrine::connect();
    $entityManager = Doctrine::getEntityManager();
} catch (\Throwable $th) {
    trigger_error('Doctrine Connection Failed!');
}

# timezone
date_default_timezone_set("Europe/Prague");

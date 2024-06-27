<?php

declare(strict_types=1);

# imports
use App\Controller\Api\Entity;
use App\Controller\Front;

# require config
require_once "../config/app.php";

# set json and cors headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

# preflight error fix
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
    die(200);

# handle api request
$api = new Entity();
$api->run();

// $api = new Front();
// $api->run();
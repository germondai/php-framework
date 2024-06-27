<?php

declare(strict_types=1);

use App\Bootstrap;

# require config
require_once "../config/app.php";

# boot app
$application = new Bootstrap;
$application->boot();

// # imports
// use App\Controller\Api\Entity;
// use App\Controller\Front;

// # handle api request
// $api = new Entity();
// $api->run();

// // $api = new Front();
// // $api->run();
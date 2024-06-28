<?php

declare(strict_types=1);

use App\Bootstrap;

# require config
require_once "../config/app.php";

# boot app
$application = new Bootstrap;
$application->boot();
<?php

// Set the base path
$basePath = __DIR__ . '/..';

// Register the Composer autoloader...
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once $basePath . '/bootstrap/app.php';

use Illuminate\Http\Request;

$app->handleRequest(Request::capture());

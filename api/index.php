<?php

// Show errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$basePath = __DIR__ . '/..';

if (!file_exists($basePath . '/vendor/autoload.php')) {
    die("CRITICAL ERROR: vendor/autoload.php is missing. Vercel did not install dependencies correctly.");
}

// Register the Composer autoloader...
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once $basePath . '/bootstrap/app.php';

use Illuminate\Http\Request;

$app->handleRequest(Request::capture());

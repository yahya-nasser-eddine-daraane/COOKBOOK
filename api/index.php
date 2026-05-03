<?php

use Illuminate\Http\Request;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$basePath = realpath(__DIR__ . '/..');

if (!file_exists($basePath . '/vendor/autoload.php')) {
    die("CRITICAL ERROR: vendor/autoload.php is missing. Vercel did not install dependencies.");
}

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

// Redirect Laravel's cache files to /tmp so they aren't read from the stale build folder
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';

$app->useStoragePath('/tmp/storage');

$app->handleRequest(Request::capture());

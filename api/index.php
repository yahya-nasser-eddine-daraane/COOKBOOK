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

$app->useStoragePath('/tmp/storage');
$app->useBootstrapPath('/tmp/bootstrap');

$app->handleRequest(Request::capture());

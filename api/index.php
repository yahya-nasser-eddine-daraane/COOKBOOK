<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$basePath = realpath(__DIR__ . '/..');

if (!file_exists($basePath . '/vendor/autoload.php')) {
    die("CRITICAL ERROR: vendor/autoload.php is missing. Vercel did not install dependencies.");
}

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

// Fix Vercel path caching issues dynamically
$app->setBasePath($basePath);

// Vercel Serverless is read-only except for /tmp
// We must redirect storage and compiled views to /tmp
$app->useStoragePath('/tmp/storage');

use Illuminate\Http\Request;

$app->handleRequest(Request::capture());

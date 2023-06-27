<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

// Add the current time to the response content
// $response->setContent($response->getContent() . '<br>Time: ' . date('Y-m-d H:i:s'));

$response->send();

// Get the Laravel application instance
$app = require_once __DIR__.'/../bootstrap/app.php';


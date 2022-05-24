<?php

require dirname(__DIR__) . "/vendor/autoload.php";
define('START_TIME', microtime(true));

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app::getContainer()->make(\App\Http\Server::class)->start();

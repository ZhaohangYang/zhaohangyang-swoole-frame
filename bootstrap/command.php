<?php

use App\Application;

$app = new Application(dirname(__DIR__, 1));
/**
 * 基础服务
 */
$app->register(\App\ServiceProvider\Basic\ConfigServiceProvider::class);

return $app;

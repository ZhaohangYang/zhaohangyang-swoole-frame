<?php
use App\Application;

$app = new Application(dirname(__DIR__, 1));
/**
 * 基础服务
 */
$app->register(\App\ServiceProvider\Basic\ConfigServiceProvider::class);
$app->register(\App\ServiceProvider\Basic\RouteServiceProvider::class);
$app->register(\App\ServiceProvider\Basic\HandlerServiceProvider::class);

return $app;

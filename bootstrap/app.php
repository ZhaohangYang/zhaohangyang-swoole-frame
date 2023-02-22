<?php

use App\Application;

$app = new Application( dirname( __DIR__, 1 ) );

/**
 * 基础服务【必须开启】
 */
$app->register( \App\ServiceProvider\Basic\ConfigServiceProvider::class);
$app->register( \App\ServiceProvider\Basic\RouteServiceProvider::class);
$app->register( \App\ServiceProvider\Basic\HandlerServiceProvider::class);

/**
 * 标准服务【选择开启】
 */
$app->register( \App\ServiceProvider\Standard\CacheServiceProvider::class);



return $app;
<?php
use App\Application;
use GuzzleHttp\DefaultHandler;

$app = new Application(dirname(__DIR__, 1));

/**
 * CURL功能依赖guzzle，启用可并发执行
 */
DefaultHandler::setDefaultHandler(SwooleHandler::class);

/**
 * 基础服务
 */
$app->register(\App\ServiceProvider\Basic\ConfigServiceProvider::class);
$app->register(\App\ServiceProvider\Basic\RouteServiceProvider::class);
$app->register(\App\ServiceProvider\Basic\HandlerServiceProvider::class);

/**
 * 扩展服务【选择开启】
 */
$this->app->register(\App\ServiceProvider\Extend\DataBaseServiceProvider::class);

/**
 * 服务启动
 */
$app->boot();

return $app;

<?php
use App\Application;

$app = new Application(dirname(__DIR__, 1));
/**
 * 基础服务
 */
$app->register(\App\Service\Basic\ConfigServiceProvider::class);
$app->register(\App\Service\Basic\RouteServiceProvider::class);
$app->register(\App\Service\Basic\HandlerServiceProvider::class);

// 启用伙伴基础服务。可选
$app->register(\App\Service\Huoban\HuobanServiceProvider::class);
// 启用Redis基础服务。可选
$app->register(\App\Service\DataBase\DataBaseServiceProvider::class);

return $app;

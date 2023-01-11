<?php
use App\Application;
use GuzzleHttp\DefaultHandler;
use Yurun\Util\Swoole\Guzzle\SwooleHandler;

$app = new Application(dirname(__DIR__, 1));
/**
 * 基础服务
 */
$app->register(\App\ServiceProvider\Basic\ConfigServiceProvider::class);

// 在你的项目入口加上这句话,启用guzzle，并发
DefaultHandler::setDefaultHandler(SwooleHandler::class);

// 启用Redis基础服务。可选
$this->app->register(\App\ServiceProvider\Swoole\DataBaseServiceProvider::class);
// 启用伙伴基础服务。可选【依赖于redis，基础服务】
$this->app->register(\App\ServiceProvider\Swoole\HuobanServiceProvider::class);

$this->app->boot();

return $app;

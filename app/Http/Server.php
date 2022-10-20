<?php

namespace App\Http;

use App\Application;
use App\ServiceProvider\Basic\HandlerService\Handler;
use GuzzleHttp\DefaultHandler;
use Illuminate\Contracts\Config\Repository;
use Yurun\Util\Swoole\Guzzle\SwooleHandler;

class Server
{

    public $app;
    public $config;

    public function __construct(Application $app, Repository $config)
    {
        $this->app    = $app;
        $this->config = $config;
    }

    public function start()
    {
        $pool = new \Swoole\Process\Pool(1);

        $pool->set(['enable_coroutine' => true]); //让每个OnWorkerStart回调都自动创建一个协程
        $pool->on('workerStart', function ($pool, $id) {
            try {
                ini_set('memory_limit', '1G');
                // 启用guzzle，并发
                DefaultHandler::setDefaultHandler(SwooleHandler::class);
                // 数据库服务【选择开启】
                $this->app->register(\App\ServiceProvider\Swoole\DataBaseServiceProvider::class);

                extract($this->config->get('swoole.server'));
                $handler = $this->app::getContainer()->make(Handler::class);

                $server = new \Swoole\Coroutine\Http\Server($host, $port + $id, $ssl);
                $server->handle('/', $handler);
                $server->start();

            } catch (\Throwable $th) {
                print_r($th->getMessage() . PHP_EOL);
            }
        });
        $pool->start();
    }
}

<?php

namespace App\Http;

use App\Application;
use App\ServiceProvider\Basic\HandlerService\Handler;
use GuzzleHttp\DefaultHandler;
use Illuminate\Contracts\Config\Repository;
use Swoole\Process;
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
        $pool = new Process\Pool(1);

        $pool->set(['enable_coroutine' => true]); //让每个OnWorkerStart回调都自动创建一个协程
        $pool->on('workerStart', function ($pool, $id) {
            try {
                ini_set('memory_limit', '1G');
                // 在你的项目入口加上这句话,启用guzzle，并发
                DefaultHandler::setDefaultHandler(SwooleHandler::class);
                // 启用伙伴基础服务。可选
                $this->app->register(\App\ServiceProvider\Swoole\HuobanServiceProvider::class);
                // 启用Redis基础服务。可选
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

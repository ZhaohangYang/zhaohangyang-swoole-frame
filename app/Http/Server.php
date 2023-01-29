<?php

namespace App\Http;

use App\Application;
use App\ServiceProvider\Basic\HandlerService\Handler;
use Illuminate\Contracts\Config\Repository;

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

                extract($this->config->get('swoole.server'));
                $handler = $this->app::getContainer()->make(Handler::class);

                $server = new \Swoole\Coroutine\Http\Server($host, $port + $id, $ssl);
                $server->handle('/', $handler);
                $server->start();

            } catch (\Throwable$th) {
                print_r($th->getMessage() . PHP_EOL);
            }
        });
        $pool->start();
    }
}

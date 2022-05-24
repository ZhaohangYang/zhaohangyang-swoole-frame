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
        ini_set('memory_limit', '4G');

        $swoole_config = $this->config->get('swoole.coroutine.options');
        $handler       = $this->app::getContainer()->make(Handler::class);

        \Swoole\Coroutine::set($swoole_config);
        $scheduler = new \Swoole\Coroutine\Scheduler;

        $scheduler->add(function () use ($handler) {
            try {

                extract($this->config->get('swoole.server'));
                $server = new \Swoole\Coroutine\Http\Server($host, $port, $ssl);
                $server->handle('/', $handler);
                $server->start();

            } catch (\Throwable $th) {
                print_r($th->getMessage());
            }
        });

        $scheduler->start();
    }
}

<?php

namespace App\Http;

use App\Application;
use Illuminate\Contracts\Config\Repository;
use Swoole\Coroutine;
use Swoole\Process;

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
        $host = $this->config->get('swoole.server.host');
        $port = $this->config->get('swoole.server.port');
        $ssl  = $this->config->get('swoole.server.ssl');

        $pool = new Process\Pool(5);
        $pool->set(['enable_coroutine' => true]);

        $pool->on('WorkerStart', function (Process\Pool $pool, $workerId) use ($host, &$port, $ssl) {
            ini_set('memory_limit', '1G');

            // $handler = $this->app::getContainer()->make(Handler::class);

            var_dump($host);
            var_dump((int) ++$port);
            var_dump($ssl);

            echo ("[Worker #{$workerId}] WorkerStart, pid: " . posix_getpid() . "\n");
            // $server = new \Swoole\Coroutine\Http\Server('0:0:0:0', 9501, false);
            // $server->handle('/', $handler);
            // $server->start();
            sleep(1000);

            // $this->startByProcessPool($swoole_config, $handler);
        });

        $pool->start();
    }

    public function startByProcessPool($swoole_config, $handler)
    {

        $pool = new Process\Pool(5);
        $pool->set(['enable_coroutine' => true]);
        $pool->on('WorkerStart', function (Process\Pool $pool, $workerId) {
            /** 当前是 Worker 进程 */
            static $running = true;
            Process::signal(SIGTERM, function () use (&$running) {
                $running = false;
                echo "TERM\n";
            });
            echo ("[Worker #{$workerId}] WorkerStart, pid: " . posix_getpid() . "\n");
            while ($running) {
                Coroutine::sleep(1);
                echo "sleep 1\n";
            }
        });
        $pool->on('WorkerStop', function (\Swoole\Process\Pool $pool, $workerId) {
            echo ("[Worker #{$workerId}] WorkerStop\n");
        });
        $pool->start();
    }
}

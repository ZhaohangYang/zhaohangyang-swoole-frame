<?php

namespace App\Http;

use App\Application;
use App\ModelsHuoban\HuobanBasic;
use App\Models\Redis\RedisBasic;
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

    /**
     * 启用伙伴插件
     *
     * @return void
     */
    public function enableHuoban()
    {
        // 在你的项目入口加上这句话
        DefaultHandler::setDefaultHandler(SwooleHandler::class);

        $huoban_config = $this->config->get('huoban.huoban_pass');
        HuobanBasic::init($huoban_config);

        \Swoole\Timer::tick(3600000, function (int $timer_id) use ($huoban_config) {
            if (date('H', time()) == 23) {
                HuobanBasic::refresh($huoban_config, $timer_id);
            }
        });
    }

    /**
     * 启用伙伴redis
     *
     * @return void
     */
    public function enableRedis()
    {
        RedisBasic::setPool();
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
                // 启用伙伴工具包
                $this->enableHuoban();
                // 启用redis服务
                $this->enableRedis();

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

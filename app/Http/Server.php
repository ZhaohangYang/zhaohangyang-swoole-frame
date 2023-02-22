<?php

namespace App\Http;

use App\Application;
use App\Helpers\BasicHelpers;
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
     *
     * 依赖于Swoole扩展服务【选择开启】
     *
     * @return void
     */
    public function swooleServiceRegister()
    {
        // CURL功能依赖guzzle，启用可并发执行
        DefaultHandler::setDefaultHandler( SwooleHandler::class);
        // 扩展服务【选择开启】
        $this->app->register( \App\ServiceProvider\Swoole\DataBaseServiceProvider::class);
    }

    /**
     *
     * 在Swoole进程中启动注册服务
     *
     * @return void
     */
    public function swooleServiceBoot()
    {
        $this->app->boot();
    }

    public function start()
    {
        $pool = new \Swoole\Process\Pool( 1 );

        $pool->set( [ 'enable_coroutine' => true ] ); //让每个OnWorkerStart回调都自动创建一个协程
        $pool->on( 'workerStart', function ($pool, $id)
        {
            try {

                $this->swooleServiceRegister();
                $this->swooleServiceBoot();

                extract( $this->config->get( 'swoole.server' ) );
                $server = new \Swoole\Coroutine\Http\Server( $host, $port + $id, $ssl );

                $handler = $this->app::getContainer()->make( Handler::class);
                $server->handle( '/', $handler );

                $server->start();
            }
            catch ( \Throwable $th ) {

                BasicHelpers::printFormatMessage( $th->getMessage() );
            }
        } );

        $pool->start();
    }
}
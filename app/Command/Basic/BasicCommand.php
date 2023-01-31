<?php

namespace App\Command\Basic;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Helpers\BasicHelpers;
use GuzzleHttp\DefaultHandler;
use Yurun\Util\Swoole\Guzzle\SwooleHandler;

class BasicCommand extends Command
{
    // 启动应用
    protected $app;
    // 配置服务
    protected $configService;


    protected static $defaultName = 'app:basic';

    protected function configure(): void
    {
        $this->addArgument('action', InputArgument::REQUIRED, '你要执行的操作');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $action = $input->getArgument('action');

        if (method_exists($this, $action)) {

            $this->app    = require dirname(__DIR__, 3) . '/bootstrap/command.php';
            $this->configService = $this->app->getContainer()->get('config');

            $this->$action($input);
        }

        return Command::SUCCESS;
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
        DefaultHandler::setDefaultHandler(SwooleHandler::class);

        // 扩展服务【选择开启】
        // $this->app->register(\App\ServiceProvider\Swoole\DataBaseServiceProvider::class);
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

    /**
     * swooleReady 预备
     *
     * @return void
     */
    public function swooleReady($callback)
    {
        $swoole_config = $this->configService->get('swoole.coroutine.options');
        \Swoole\Coroutine::set($swoole_config);

        $scheduler = new \Swoole\Coroutine\Scheduler;
        $scheduler->add(function () use ($callback) {

            $this->swooleServiceRegister();
            $this->swooleServiceBoot();

            try {
                call_user_func($callback);
            } catch (\Throwable $th) {
                BasicHelpers::printFormatMessage($th->getMessage());
            }
        });

        $scheduler->start();
    }
}

<?php

namespace App\Command\Basic;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BasicCommand extends Command
{
    public $app;
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
            $this->config = $this->app->getContainer()->get('config');

            $this->$action($input);
        }

        return Command::SUCCESS;
    }

    /**
     * swooleReady 预备
     *
     * @return void
     */
    public function swooleReady($callback)
    {
        $swoole_config = $this->config->get('swoole.coroutine.options');
        \Swoole\Coroutine::set($swoole_config);

        $this->scheduler = new \Swoole\Coroutine\Scheduler;
        $this->scheduler->add(function () use ($callback) {

            try {
                call_user_func($callback);
            } catch (\Throwable $th) {
                print_r($th->getMessage());
            }
        });

        $this->scheduler->start();
    }

}

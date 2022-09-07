<?php

namespace App\Command\Init;

use App\Command\Basic\BasicCommand;
use Symfony\Component\Console\Input\InputArgument;

class InitCommand extends BasicCommand
{
    protected static $defaultName        = 'app:init';
    protected static $defaultDescription = '初始化基础数据维护';

    protected function configure(): void
    {
        $this->addArgument('action', InputArgument::REQUIRED, '你要执行的操作');
    }

    public function initAll()
    {
        $methods = [];
        foreach ($methods as $method) {
            $this->$method();
        }

    }

}

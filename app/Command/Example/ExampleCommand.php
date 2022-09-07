<?php

namespace App\Command\Example;

use App\Command\Basic\BasicCommand;
use Symfony\Component\Console\Command\Command;

class ExampleCommand extends BasicCommand
{
    protected static $defaultName        = 'app:init';
    protected static $defaultDescription = '实例操作';

}

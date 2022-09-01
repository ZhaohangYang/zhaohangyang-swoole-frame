<?php

namespace App\Http\Controller;

use App\Application;
use App\Http\Controller\BasicController;

class IndexController extends BasicController
{
    public function test($params)
    {
        $huobanBasic = \App\Application::getContainer()->get('HuobanBasic');

        var_dump($huobanBasic->huoban->getConfig('ticket'));

        $redisBasic = \App\Application::getContainer()->get('redisBasic');

    }
}

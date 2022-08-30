<?php

namespace App\Http\Controller;

use App\Http\Controller\BasicController;

class IndexController extends BasicController
{
    public function test($params)
    {
        $huobanBasic = \App\Application::getContainer()->get('huobanBasic');
        $redisBasic  = \App\Application::getContainer()->get('redisBasic');
    }
}

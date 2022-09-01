<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */

namespace App\ServiceProvider\Huoban\Service;

use Huoban\Huoban;

class HuobanBasic
{
    public $huobanConfig;

    public $huoban;
    public $huobanItem;

    public $logPath;

    public function __construct($huoban_config)
    {
        $this->huobanConfig = $huoban_config;

        $this->timer();
        $this->refresh(true);
    }

    public function timer()
    {
        \Swoole\Timer::tick(3600000, function (int $timer_id) {
            if (date('H', time()) == 23) {
                $this->refresh();
            }
        });
    }

    public function refresh()
    {
        $this->refreshHuoban();
        $this->refreshHuobanItem();
    }

    public function refreshHuoban()
    {
        $this->huoban = new Huoban($this->huobanConfig);
    }

    public function refreshHuobanItem()
    {
        $this->huobanItem = $this->huoban->make('item');
    }

}

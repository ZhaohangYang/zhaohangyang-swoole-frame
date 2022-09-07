<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ServiceProvider\Swoole;

use App\Models\Huoban\HuobanBasic;
use App\ServiceProvider\BasicServiceProvider;

class HuobanServiceProvider extends BasicServiceProvider
{
    public $huobanConfig;

    public $huoban;
    public $huobanItem;

    public $logPath;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register($huoban_persistence_enable = false)
    {
        $serviceConfig = $this->container->get('config');

        $huoban_pass_config  = $serviceConfig->get('huoban.huoban_pass');
        $huoban_basic_config = $serviceConfig->get('huoban.huoban_basic');

        $huoban_pass_config = array_merge($huoban_basic_config, $huoban_pass_config);

        HuobanBasic::enable($huoban_pass_config, $huoban_persistence_enable);

    }

}

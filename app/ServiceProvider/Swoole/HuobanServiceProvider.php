<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ServiceProvider\Swoole;

use App\ServiceProvider\BasicServiceProvider;
use App\ServiceProvider\Swoole\HuobanService\HuobanBasic;

class HuobanServiceProvider extends BasicServiceProvider
{
    public $huobanConfig;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->setConfig();
        HuobanBasic::enable($this->huobanConfig);
    }

    public function boot()
    {
    }

    public function setConfig()
    {
        $serviceConfig = $this->container->get('config');

        $huoban_pass_config  = $serviceConfig->get('huoban.huoban_pass');
        $huoban_basic_config = $serviceConfig->get('huoban.huoban_basic');

        $this->huobanConfig = array_merge($huoban_basic_config, $huoban_pass_config);
    }

}

<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ServiceProvider\Huoban;

use App\Application;
use App\ServiceProvider\BasicServiceProvider;
use App\ServiceProvider\Huoban\Service\HuobanBasic;
use Huoban\Helpers\HuobanVerify;

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
    public function register()
    {
        $serviceConfig = $this->container->get('config');
        $huoban_config = $serviceConfig->get('huoban.space_one');

        $this->container->singleton('HuobanBasic', function () use ($huoban_config) {
            return new HuobanBasic($huoban_config);
        });

        $this->enableHuobanVerify();
    }

    public function enableHuobanVerify()
    {
        $storage_path = Application::StoragePath();
        HuobanVerify::init($storage_path);
    }

}

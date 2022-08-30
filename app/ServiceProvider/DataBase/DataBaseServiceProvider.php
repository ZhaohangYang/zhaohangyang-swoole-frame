<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ServiceProvider\DataBase;

use App\ServiceProvider\BasicServiceProvider;
use App\ServiceProvider\DataBase\Service\RedisBasic;

class DataBaseServiceProvider extends BasicServiceProvider
{

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
        $redis_config  = $serviceConfig->get('redis.default');

        $this->container->singleton('redisBasic', function () use ($redis_config) {
            return new RedisBasic($redis_config);
        });
    }

}

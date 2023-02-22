<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ServiceProvider\Swoole;

use App\ServiceProvider\Swoole\DataBaseService\MysqlBasic;
use App\ServiceProvider\Swoole\DataBaseService\RedisBasic;
use App\ServiceProvider\BasicServiceProvider;

/**
 * SWOOLE数据库基础服务
 */
class DataBaseServiceProvider extends BasicServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $serviceConfig = $this->container->get('config');

        $redis_config = $serviceConfig->get('database.redis');
        $redis_config['enable'] && RedisBasic::enable($redis_config);

        $mysql_config = $serviceConfig->get('database.mysql');
        $mysql_config['enable'] && MysqlBasic::enable($mysql_config);
    }

    public function boot()
    {
    }

}
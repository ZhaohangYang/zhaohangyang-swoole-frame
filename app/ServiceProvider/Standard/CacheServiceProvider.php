<?php
namespace App\ServiceProvider\Standard;

use App\ServiceProvider\BasicServiceProvider;
use App\ServiceProvider\Swoole\DataBaseService\RedisBasic;
use App\ServiceProvider\Standard\CacheService\CacheBasic;

/**
 * SWOOLE缓存基础服务【redis,mysql缓存依赖，SWOOLE数据库基础服务】
 */
class CacheServiceProvider extends BasicServiceProvider
{

    public function register()
    {
        $serviceConfig = $this->container->get( 'config' );

        CacheBasic::enableFileCache();

        $redis_config = $serviceConfig->get( 'database.redis' );
        if ( $redis_config['enable'] ) {

            CacheBasic::enableRedisCache( $redis_config );
        }
    }

}
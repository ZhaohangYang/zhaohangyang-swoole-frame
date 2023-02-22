<?php

namespace App\ServiceProvider\Standard\CacheService;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheBasic
{
    public static $fileCache;
    public static $redisCache;
    public static function enableFileCache()
    {
        self::$fileCache = new FilesystemAdapter();
    }

    /**
     * 启用redis 缓存
     *
     * @param array $redis_config
     * @return void
     */
    public static function enableRedisCache(array $redis_config)
    {
        $redisConnection = RedisAdapter::createConnection(
            'redis://' . $redis_config['ip'] . ':' . $redis_config['port']
        );

        self::$redisCache = new RedisAdapter(
            // the object that stores a valid connection to your Redis system
            // [直译]   存储到Redis系统的有效连接的对象
            $redisConnection,
            // the string prefixed to the keys of the items stored in this cache
            // [直译]   此缓存中存储的项的关键字的前缀字符串[直译]
            $namespace = '',
            // the default lifetime (in seconds) for cache items that do not define their
            // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
            // until RedisAdapter::clear() is invoked or the server(s) are purged)
            // [直译]   未定义缓存项的默认生存期（秒）自己的生存期，值为0会导致项目无限期存储（即直到调用RedisAdapter:：clear（）或清除服务器）
            $defaultLifetime = 0
        );

    }


}
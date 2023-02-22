<?php

namespace App\Models\Cache;

use App\ServiceProvider\Standard\CacheService\CacheBasic;
use Symfony\Contracts\Cache\ItemInterface;

class CacheExample
{

    public function getCache($cache)
    {
        // The callable will only be executed on a cache miss.
        // [直译] 仅在缓存未命中时执行可调用。
        $value = $cache->get( 'my_cache_key', function (ItemInterface $item)
        {
            $item->expiresAfter( 3600 );

            // ... do some HTTP request or heavy computations
            // [直译] 执行一些HTTP请求或繁重的计算
            $computedValue = 'test';

            return $computedValue;
        } );

        return $value;
    }

    public function deleteCache($cache)
    {
        $cache->delete( 'my_cache_key' );
    }


    public function redisGetCache()
    {
        $this->getCache( CacheBasic::$redisCache );
    }

    public function rediDeleteCache()
    {
        $this->deleteCache( CacheBasic::$redisCache );
    }

}
<?php
namespace App\Models\Redis;

use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

class RedisBasic
{
    public static $redis;
    public static $pool;
    public static function init()
    {
        self::setPool();
        self::setRedis();
    }

    public static function setPool($number = 200)
    {
        self::$pool = new RedisPool((new RedisConfig)
                ->withHost('127.0.0.1')
                ->withPort(6379)
                ->withAuth('')
                ->withDbIndex(0)
                ->withTimeout(1)
            , $number);

        self::$pool->fill();
    }

    public static function getPool()
    {
        return self::$pool;
    }

    public static function setRedis()
    {
        self::$redis = new \Redis();
        self::$redis->connect('127.0.0.1', 6379); //此处产生协程调度，cpu切到下一个协程(下一个请求)，不会阻塞进程
    }

    public static function getRedis()
    {
        return self::$redis; //此处产生协程调度，cpu切到下一个协程(下一个请求)，不会阻塞进程
    }

    public static function getNewRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        return $redis; //此处产生协程调度，cpu切到下一个协程(下一个请求)，不会阻塞进程
    }

    public static function set($key, $value)
    {
        $redis = self::$pool->get();
        $redis->set($key, $value);
        self::$pool->put($redis);
    }

    public static function get($key)
    {
        $redis = self::$pool->get();
        $value = $redis->get($key);
        self::$pool->put($redis);

        return $value;
    }

    public static function smembers($key)
    {
        $redis    = self::$pool->get();
        $smembers = $redis->smembers($key);
        self::$pool->put($redis);

        return $smembers;
    }

    public static function lpush($key, $value)
    {
        $redis  = self::$pool->get();
        $status = $redis->lpush($key, $value);
        self::$pool->put($redis);

        return $status;
    }

    public static function blpop($key, $length)
    {
        $redis = self::$pool->get();
        $value = $redis->blpop($key, $length);
        self::$pool->put($redis);

        return $value;
    }

    public static function combination($params)
    {
        $redis = self::$pool->get();

        $results = [];
        foreach ($params as $index => $action_params) {
            $action = $action_params['action'];
            $params = $action_params['params'];

            switch ($action) {
                case 'get':
                    $results_tmp = $redis->get($params['key']);
                case 'set':
                    $results_tmp = $redis->set($params['key'], $params['value']);
                case 'sadd':
                    $results_tmp = $redis->sadd($params['key'], $params['value']);
                    break;
                default:
                    $results_tmp = null;
                    break;
            }
            $results[$index] = $results_tmp;
        }

        self::$pool->put($redis);
        return $results;
    }
}

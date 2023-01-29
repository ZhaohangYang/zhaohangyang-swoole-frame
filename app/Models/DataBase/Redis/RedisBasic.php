<?php
namespace App\Models\DataBase\Redis;

use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

class RedisBasic
{
    public static $redis;
    public static $pool;
    public static $config;

    public static function enable($redis_config)
    {
        self::$config = $redis_config;

        self::setPool();
        self::setRedis();
    }

    public static function setPool()
    {
        self::$pool = new RedisPool((new RedisConfig)
                ->withHost(self::$config['ip'])
                ->withPort(self::$config['port'])
                ->withAuth(self::$config['password'])
                ->withDbIndex(self::$config['db_index'])
                ->withTimeout(self::$config['time_out'])
            , self::$config['number']);

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

    public static function del($key)
    {
        $redis = self::$pool->get();
        $value = $redis->del($key);
        self::$pool->put($redis);

        return $value;
    }

    public static function hset($hash, $key, $value)
    {
        $redis = self::$pool->get();
        $redis->hset($hash, $key, $value);
        self::$pool->put($redis);
    }

    public static function hmset($hash, $fields_values)
    {
        $redis = self::$pool->get();
        $redis->hmset($hash, $fields_values);
        self::$pool->put($redis);
    }

    public static function hget($hash, $key)
    {

        $redis = self::$pool->get();
        $value = $redis->hget($hash, $key);
        self::$pool->put($redis);
        return $value;
    }
    public static function hdel($hash, $key)
    {
        $redis = self::$pool->get();
        $value = $redis->hdel($hash, $key);
        self::$pool->put($redis);

        return $value;
    }
    public static function hgetall($hash)
    {
        $redis = self::$pool->get();
        $value = $redis->hGetAll($hash);
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
    public static function sadd($key, $value)
    {
        $redis  = self::$pool->get();
        $status = $redis->sadd($key, $value);
        self::$pool->put($redis);

        return $status;
    }
    public static function sismember($key, $member)
    {
        $redis  = self::$pool->get();
        $status = $redis->sismember($key, $member);
        self::$pool->put($redis);

        return $status;
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

    public static function setByRedis($key, $value, $redis = null)
    {
        $value = $redis ? $redis->set($key, $value) : self::set($key, $value);
        return $value;
    }

    public static function getByRedis($key, $redis = null)
    {
        $value = $redis ? $redis->get($key) : self::get($key);
        return $value;
    }

    public static function delByRedis($key, $redis = null)
    {
        $value = $redis ? $redis->del($key) : self::del($key);
        return $value;
    }

    public static function hsetByRedis($hash, $key, $value, $redis = null)
    {
        $value = $redis ? $redis->hset($hash, $key, $value) : self::hset($hash, $key, $value);
        return $value;
    }

    public static function hmsetByRedis($hash, $fields_values, $redis = null)
    {
        $value = $redis ? $redis->hmset($hash, $fields_values) : self::hmset($hash, $fields_values);
        return $value;
    }

    public static function hgetByRedis($hash, $key, $redis = null)
    {
        $value = $redis ? $redis->hget($hash, $key) : self::hget($hash, $key);
        return $value;
    }
    public static function hdelByRedis($hash, $key, $redis = null)
    {
        $value = $redis ? $redis->hdel($hash, $key) : self::hdel($hash, $key);
        return $value;
    }
    public static function hgetallByRedis($hash, $redis = null)
    {
        $value = $redis ? $redis->hGetAll($hash) : self::hGetAll($hash);
        return $value;
    }

    public static function smembersByRedis($key, $redis = null)
    {
        $smembers = $redis ? $redis->smembers($key) : self::smembers($key);
        return $smembers;
    }
    public static function saddByRedis($key, $value, $redis = null)
    {
        $status = $redis ? $redis->sadd($key, $value) : self::sadd($key, $value);
        return $status;
    }
    public static function sismemberByRedis($key, $member, $redis = null)
    {
        $status = $redis ? $redis->sismember($key, $member) : self::sismember($key, $member);
        return $status;
    }

    public static function lpushByRedis($key, $value, $redis = null)
    {
        $status = $redis ? $redis->lpush($key, $value) : self::lpush($key, $value);
        return $status;
    }

    public static function blpopByRedis($key, $length, $redis = null)
    {

        $value = $redis ? $redis->blpop($key, $length) : self::blpop($key, $length);
        return $value;
    }

}

<?php
namespace App\Service\DataBase\Provider;

use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

class RedisBasic
{
    public $redis;
    public $pool;

    public function __construct($redis_config)
    {
        $this->redisConfig = $redis_config;
        $this->setPool();
    }

    public function setPool()
    {
        $this->pool = new RedisPool((new RedisConfig)
                ->withHost($this->redisConfig['ip'])
                ->withPort($this->redisConfig['port'])
                ->withAuth($this->redisConfig['password'])
                ->withDbIndex($this->redisConfig['db_index'])
                ->withTimeout($this->redisConfig['time_out'])
            , $this->redisConfig['number']);

        $this->pool->fill();
    }

    public function getPool()
    {
        return $this->pool;
    }

    public function set($key, $value)
    {
        $redis = $this->pool->get();
        $redis->set($key, $value);
        $this->pool->put($redis);
    }

    public function get($key)
    {
        $redis = $this->pool->get();
        $value = $redis->get($key);
        $this->pool->put($redis);

        return $value;
    }

    public function del($key)
    {
        $redis = $this->pool->get();
        $value = $redis->del($key);
        $this->pool->put($redis);

        return $value;
    }

    public function hset($hash, $key, $value)
    {
        $redis = $this->pool->get();
        $redis->hset($hash, $key, $value);
        $this->pool->put($redis);
    }

    public function hmset($hash, $fields_values)
    {
        $redis = $this->pool->get();
        $redis->hmset($hash, $fields_values);
        $this->pool->put($redis);
    }

    public function hget($hash, $key)
    {
        $redis = $this->pool->get();
        $value = $redis->hget($hash, $key);
        $this->pool->put($redis);

        return $value;
    }

    public function hgetall($hash)
    {
        $redis = $this->pool->get();
        $value = $redis->hgetall($hash);
        $this->pool->put($redis);

        return $value;
    }

    public function hdel($hash, $key)
    {
        $redis = $this->pool->get();
        $value = $redis->hdel($hash, $key);
        $this->pool->put($redis);

        return $value;
    }

    public function sadd($key, $value)
    {
        $redis   = $this->pool->get();
        $results = $redis->sadd($key, $value);
        $this->pool->put($redis);

        return $results;
    }

    public function smembers($key)
    {
        $redis    = $this->pool->get();
        $smembers = $redis->smembers($key);
        $this->pool->put($redis);

        return $smembers;
    }

    public function sismember($key, $member)
    {
        $redis  = $this->pool->get();
        $status = $redis->sismember($key, $member);
        $this->pool->put($redis);

        return $status;
    }

    public function lpush($key, $value)
    {
        $redis  = $this->pool->get();
        $status = $redis->lpush($key, $value);
        $this->pool->put($redis);

        return $status;
    }

    public function lpop($key)
    {
        $redis = $this->pool->get();
        $value = $redis->lpop($key);
        $this->pool->put($redis);

        return $value;
    }

    public function blpop($key, $length)
    {
        $redis = $this->pool->get();
        // 如果一直等待，设置超时时间为永久不超时
        0 == $length && $this->setTimeoOutNever($redis);

        $value = $redis->blpop($key, $length);
        $this->pool->put($redis);

        return $value;
    }

    public function publish($channels)
    {
        $redis = $this->pool->get();
        foreach ($channels as $channel => $params) {
            $status = $redis->PUBLISH($channel, $params);
        }
        $this->pool->put($redis);

        return $status;
    }

    public function subscribe($channels, $callback)
    {
        $redis = $this->pool->get();
        $this->setTimeoOutNever($redis);

        $redis->subscribe($channels, function ($redis, $channel, $message) use ($callback) {
            $callback($redis, $channel, $message);
        });

        $this->pool->put($redis);
    }

    public function combination($params)
    {
        $redis = $this->pool->get();

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
                case 'lpush':
                    $results_tmp = $redis->lpush($params['key'], $params['value']);
                    break;
                default:
                    $results_tmp = null;
                    break;
            }
            $results[$index] = $results_tmp;
        }

        $this->pool->put($redis);
        return $results;
    }

    public function setByRedis($key, $value, $redis = null)
    {
        $value = $redis ? $redis->set($key, $value) : $this->set($key, $value);
        return $value;
    }

    public function getByRedis($key, $redis = null)
    {
        $value = $redis ? $redis->get($key) : $this->get($key);
        return $value;
    }

    public function delByRedis($key, $redis = null)
    {
        $value = $redis ? $redis->del($key) : $this->del($key);
        return $value;
    }

    public function hsetByRedis($hash, $key, $value, $redis = null)
    {
        $value = $redis ? $redis->hset($hash, $key, $value) : $this->hset($hash, $key, $value);
        return $value;
    }

    public function hmsetByRedis($hash, $fields_values, $redis = null)
    {
        $value = $redis ? $redis->hmset($hash, $fields_values) : $this->hmset($hash, $fields_values);
        return $value;
    }

    public function hgetByRedis($hash, $key, $redis = null)
    {
        $value = $redis ? $redis->hget($hash, $key) : $this->hget($hash, $key);
        return $value;
    }
    public function hdelByRedis($hash, $key, $redis = null)
    {
        $value = $redis ? $redis->hdel($hash, $key) : $this->hdel($hash, $key);
        return $value;
    }
    public function hgetallByRedis($hash, $redis = null)
    {

        $value = $redis ? $redis->hgetall($hash) : $this->hgetall($hash);
        return $value;
    }

    public function smembersByRedis($key, $redis = null)
    {
        $smembers = $redis ? $redis->smembers($key) : $this->smembers($key);
        return $smembers;
    }

    public function sismemberByRedis($key, $member, $redis = null)
    {
        $status = $redis ? $redis->sismember($key, $member) : $this->sismember($key, $member);
        return $status;
    }

    public function saddByRedis($key, $value, $redis = null)
    {
        $status = $redis ? $redis->sadd($key, $value) : $this->sadd($key, $value);
        return $status;
    }

    public function lpushByRedis($key, $value, $redis = null)
    {
        $status = $redis ? $redis->lpush($key, $value) : $this->lpush($key, $value);
        return $status;
    }

    public function blpopByRedis($key, $length, $redis = null)
    {

        $value = $redis ? $redis->blpop($key, $length) : $this->blpop($key, $length);
        return $value;
    }

    public function setTimeoOutNever($redis)
    {
        $redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
    }

}

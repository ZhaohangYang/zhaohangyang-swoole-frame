<?php

namespace App\ServiceProvider\Basic\ConfigService;

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Arr;

class Config implements ArrayAccess, ConfigContract
{
    /**
     * 所有的配置数据
     *
     * @var array
     */
    protected $items = [];

    /**
     * 创建新的配置存储库。
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * 确定给定的配置值是否存在。
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);
    }

    /**
     * 获取指定的配置值
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return Arr::get($this->items, $key, $default);
    }

    /**
     * 获取多个配置值。
     *
     * @param  array  $keys
     * @return array
     */
    public function getMany($keys)
    {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }

            $config[$key] = Arr::get($this->items, $key, $default);
        }

        return $config;
    }

    /**
     * 设置给定的配置值。
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }
    }

    /**
     * 将值前置到数组配置值上。
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * 将值推送到阵列配置值上。
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * 获取应用程序的所有配置项。
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * 获取一个配置选项。
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * 获取一个配置选项。
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetExists($key): bool
    {
        return $this->has($this->items, $key);
    }

    /**
     * 设置一个配置选项。
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * 删除一个配置选项。
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        Arr::forget($this->items, $key);
    }
}

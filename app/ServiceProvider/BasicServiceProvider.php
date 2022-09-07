<?php

namespace App\ServiceProvider;

use Closure;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

abstract class BasicServiceProvider extends ServiceProvider
{
    /**
     * 应用程序容器
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * 应用程序启动（boot）之前的回调函数集合
     *
     * @var array
     */
    protected $bootingCallbacks = [];

    /**
     * 应用程序启动（boot）之后的回调函数集合。
     *
     * @var array
     */
    protected $bootedCallbacks = [];

    /**
     * 应该发布的路径。
     *
     * @var array
     */
    public static $publishes = [];

    /**
     * 应按组发布的路径。
     *
     * @var array
     */
    public static $publishGroups = [];

    /**
     * 创建一个新的服务提供者实例。
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * 在应用程序中的注册函数
     *
     * @return void
     */
    public function register()
    {}

    /**
     * 添加制定的函数到，应用程序启动（boot）之前的回调函数集合
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function booting(Closure $callback)
    {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * 添加制定的函数到，应用程序启动（boot）之后的回调函数集合
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function booted(Closure $callback)
    {
        $this->bootedCallbacks[] = $callback;
    }

    /**
     * 依次调用应用程序启动（boot）之前的回调函数集合
     *
     * @return void
     */
    public function callBootingCallbacks()
    {
        $index = 0;

        while ($index < count($this->bootingCallbacks)) {
            $this->container->call($this->bootingCallbacks[$index]);

            $index++;
        }
    }

    /**
     * 依次调用应用程序启动（boot）之后的回调函数集合
     *
     * @return void
     */
    public function callBootedCallbacks()
    {
        $index = 0;

        while ($index < count($this->bootedCallbacks)) {
            $this->app->call($this->bootedCallbacks[$index]);

            $index++;
        }
    }

    /**
     * 在解析侦听器后设置侦听器，如果已解析，则立即启动。
     *
     * @param  string  $name
     * @param  \Closure|null   $callback
     * @return void
     */
    protected function callAfterResolving($name, $callback)
    {
        $this->container->afterResolving($name, $callback);

        if ($this->container->resolved($name)) {
            $callback($this->container->make($name), $this->app);
        }
    }

    /**
     * 注册要由publish命令发布的路径。
     *
     * @param  array  $paths
     * @param  mixed  $groups
     * @return void
     */
    protected function publishes(array $paths, $groups = null)
    {
        $this->ensurePublishArrayInitialized($class = static::class);

        static::$publishes[$class] = array_merge(static::$publishes[$class], $paths);

        foreach ((array) $groups as $group) {
            $this->addPublishGroup($group, $paths);
        }
    }

    /**
     * 确保已初始化服务提供程序的发布数组。
     *
     * @param  string  $class
     * @return void
     */
    protected function ensurePublishArrayInitialized($class)
    {
        if (!array_key_exists($class, static::$publishes)) {
            static::$publishes[$class] = [];
        }
    }

    /**
     * 向服务提供商添加发布组/标记。
     *
     * @param  string  $group
     * @param  array  $paths
     * @return void
     */
    protected function addPublishGroup($group, $paths)
    {
        if (!array_key_exists($group, static::$publishGroups)) {
            static::$publishGroups[$group] = [];
        }

        static::$publishGroups[$group] = array_merge(
            static::$publishGroups[$group], $paths
        );
    }

    /**
     * 获取要发布的路径。
     *
     * @param  string|null  $provider
     * @param  string|null  $group
     * @return array
     */
    public static function pathsToPublish($provider = null, $group = null)
    {
        if (!is_null($paths = static::pathsForProviderOrGroup($provider, $group))) {
            return $paths;
        }

        return collect(static::$publishes)->reduce(function ($paths, $p) {
            return array_merge($paths, $p);
        }, []);
    }

    /**
     * 获取提供程序或组（或两者）的路径。
     *
     * @param  string|null  $provider
     * @param  string|null  $group
     * @return array
     */
    protected static function pathsForProviderOrGroup($provider, $group)
    {
        if ($provider && $group) {
            return static::pathsForProviderAndGroup($provider, $group);
        } elseif ($group && array_key_exists($group, static::$publishGroups)) {
            return static::$publishGroups[$group];
        } elseif ($provider && array_key_exists($provider, static::$publishes)) {
            return static::$publishes[$provider];
        } elseif ($group || $provider) {
            return [];
        }
    }

    /**
     * 获取提供程序和组的路径。
     *
     * @param  string  $provider
     * @param  string  $group
     * @return array
     */
    protected static function pathsForProviderAndGroup($provider, $group)
    {
        if (!empty(static::$publishes[$provider]) && !empty(static::$publishGroups[$group])) {
            return array_intersect_key(static::$publishes[$provider], static::$publishGroups[$group]);
        }

        return [];
    }

    /**
     * 获取可用于发布的服务提供程序。
     *
     * @return array
     */
    public static function publishableProviders()
    {
        return array_keys(static::$publishes);
    }

    /**
     * 获取可用于发布的组。
     *
     * @return array
     */
    public static function publishableGroups()
    {
        return array_keys(static::$publishGroups);
    }

    /**
     * 获取提供商提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * 获取触发此服务提供商注册的事件。
     *
     * @return array
     */
    public function when()
    {
        return [];
    }

    /**
     * 确定是否延迟提供程序。
     *
     * @return bool
     */
    public function isDeferred()
    {
        return $this instanceof DeferrableProvider;
    }
}

<?php
namespace App;

use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class Application
{
    /**
     * 项目根路径
     *
     * @var [type]
     */
    private static $basicPath;

    /**
     * 应用容器
     *
     * @var [type]
     */
    private static $container;

    /**
     * 程序是否启动
     *
     * @var [bool]
     */
    public $booted = false;

    /**
     * 启动应用程序前需要回调的函数集合
     *
     * @var array
     */
    public $bootingCallbacks = [];

    /**
     * 启动应用程序后需要回调的函数集合
     *
     * @var array
     */
    public $bootedCallbacks = [];

    /**
     * 服务集合
     *
     * @var [type]
     */
    public $serviceProviders = [];

    /**
     * 已经加载的服务
     *
     * @var array
     */
    public $loadedProviders = [];

    public function __construct($basic_path)
    {
        self::$basicPath = $basic_path;
        self::$container = Container::getInstance();

        self::$container->instance('app', $this);
        self::$container->alias('app', Application::class);

        self::$container->instance(Container::class, self::$container);

        $this->registerBaseServiceProvider();
    }

    /**
     * 启动应用程序的服务提供程序。
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->booted) {

            //应用程序启动时，触发一些“启动”回调
            //启用监听器等等
            $this->fireAppCallbacks($this->bootingCallbacks);

            array_walk($this->serviceProviders, function ($provider) {
                $this->bootProvider($provider);
            });
            $this->booted = true;

            $this->fireAppCallbacks($this->bootedCallbacks);
        }
    }

    /**
     * 依次执行给定的函数数组
     *
     * @param array $callbacks
     * @return void
     */
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * 注册应用基础服务
     *
     * @return void
     */
    protected function registerBaseServiceProvider()
    {
    }

    /**
     * 注册服务
     *
     * @param [type] $provider
     * @param array $options
     * @param boolean $force
     * @return void
     */
    public function register($provider, $options = [], $force = false)
    {
        // 如果该服务已经被注册过，并且没有明确说明需要重新注册则直接返回已注册的服务
        if ($registered = $this->getProvider($provider) && !$force) {
            return $registered;
        }

        // 如果传入的服务是一个字符串，就尝试解析它
        if (is_string($provider)) {
            $provider = $this->resolveProviderClass($provider);
        }

        $provider->register();

        //一旦我们注册了服务，我们将遍历这些选项，并在应用程序上设置它们，
        //在服务对象的实际加载中，提供给开发人员使用。
        foreach ($options as $key => $value) {
            $this[$key] = $value;
        }

        $this->markAsRegistered($provider);

        //如果应用程序已经启动，重新调用服务的boot方法，
        //如果未启动不调用，因为程序启动之后会统一把已注册的服务全部执行各自的boot方法
        if ($this->booted) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * 获取已注册的辅助中获取服务实例，如果该服务没有注册过，返回null
     *
     * @param [type] $provider
     * @return void
     */
    public function getProvider($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        // 遍历已注册的服务数组，返回第一个符合该服务名称的服务实例，如果不存在返回null
        return Arr::first($this->serviceProviders, function ($key, $value) use ($name) {
            return $value instanceof $name;
        });
    }

    /**
     * 解析该服务，并返回一个该服务实例
     *
     * @param string $provider
     * @return ServiceProvider
     */
    public function resolveProviderClass($provider)
    {
        return new $provider(self::$container);
    }

    /**
     * 给服务标识已注册
     *
     * @param ServiceProvider $provider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $class = get_class($provider);

        $this->serviceProviders[] = $provider;

        $this->loadedProviders[$class] = true;
    }

    /**
     * Boot the given service provider.
     *
     * @param ServiceProvider $provider
     * @return mixed
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            return self::$container->call([$provider, 'boot']);
        }
    }

    /**
     * 获取应用程序的容器
     *
     * @return
     */
    public static function getContainer()
    {
        return self::$container;
    }

    public static function ConfigPath($path = '')
    {
        return self::$basicPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $path;
    }

    public static function ResourcesPath($path = '')
    {
        return self::$basicPath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $path;
    }

    public static function StoragePath($path = '')
    {
        return self::$basicPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $path;
    }
}

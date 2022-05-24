<?php
namespace App\ServiceProvider\Basic;

use App\ServiceProvider\BasicServiceProvider;
use App\ServiceProvider\RouteService\Route;
use Closure;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispather;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

class RouteServiceProvider extends BasicServiceProvider
{

    public function register()
    {
        // 容器实例一个路由收集器
        $this->container->singleton(RouteCollector::class, function () {
            $stdParser       = new Std();
            $groupCountBased = new GroupCountBased();
            $collector       = new RouteCollector($stdParser, $groupCountBased);

            $this->loadRoute($collector);
            return $collector;
        });

        // 实例一个分组计数的，Dispather
        $this->container->singleton(GroupCountBasedDispather::class, function () {
            /** @var RouteCollector $collector */
            $collector = $this->container->make(RouteCollector::class);
            return new GroupCountBasedDispather($collector->getData());
        });

    }

    public function booting(Closure $callback)
    {
    }

    /**
     * 加载配置路由
     *
     * @param RouteCollector $routeCollector
     * @return void
     */
    public function loadRoute(RouteCollector $routeCollector)
    {
        $config = $this->container->get('config');

        $callback = include $config['route.apiPath'];
        $callback($routeCollector);
    }

    public function handle($controller, $request)
    {
        $this->container->make($controller);

    }

}

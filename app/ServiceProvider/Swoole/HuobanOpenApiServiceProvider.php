<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ServiceProvider\Swoole;

use App\Models\HuobanOpenApi\HuobanOpenApiBasic;
use App\ServiceProvider\BasicServiceProvider;

class HuobanOpenApiServiceProvider extends BasicServiceProvider
{
    public $huobanOpenApiConfig;

    public $huoban;
    public $huobanItem;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $serviceConfig         = $this->container->get('config');
        $huoban_openapi_config = $serviceConfig->get('huoban_openapi.default');

        HuobanOpenApiBasic::enable($huoban_openapi_config);
    }

}

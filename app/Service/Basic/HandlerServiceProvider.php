<?php

namespace App\Service\Basic;

use App\Service\BasicServiceProvider;
use App\Service\Basic\Provider\Handler;
use Laminas\Stratigility\MiddlewarePipe;

class HandlerServiceProvider extends BasicServiceProvider
{

    public function register()
    {

        $this->container->singleton(MiddlewarePipe::class, function () {
            return new MiddlewarePipe();
        });

        $this->container->singleton(Handler::class, function () {
            return new Handler($this->container, $this->container->make(MiddlewarePipe::class));
        });
    }
}

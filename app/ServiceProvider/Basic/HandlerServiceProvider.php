<?php

namespace App\ServiceProvider\Basic;

use App\ServiceProvider\BasicServiceProvider;
use App\ServiceProvider\Basic\HandlerService\Handler;
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

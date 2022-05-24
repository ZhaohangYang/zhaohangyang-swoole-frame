<?php

namespace App\Http\Controller;

use Illuminate\Container\Container;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IndexController implements RequestHandlerInterface
{

    protected $container;

    protected $factory;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        return new JsonResponse(['message' => 'ok']);
    }

}

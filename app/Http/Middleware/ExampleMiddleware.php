<?php
namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ExampleMiddleware implements MiddlewareInterface
{

    public function __construct()
    {
    }

    public function process(ServerRequestInterface $psr_request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($psr_request);
    }
}

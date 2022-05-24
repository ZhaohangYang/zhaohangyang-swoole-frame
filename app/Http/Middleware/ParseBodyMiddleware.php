<?php
namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ParseBodyMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $psr_request, RequestHandlerInterface $handler): ResponseInterface
    {
        $content_type = current($psr_request->getHeader('content-type'));
        if ('application/json' == $content_type) {
            $psr_request = $psr_request->withParsedBody(json_decode($psr_request->getBody(), true));
        }

        return $handler->handle($psr_request);
    }
}

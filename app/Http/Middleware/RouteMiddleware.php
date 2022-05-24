<?php
namespace App\Http\Middleware;

use Exception;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispather;
use Illuminate\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * 容器
     *
     * @var [type]
     */
    public $container;
    /**
     * 路由调度对象
     *
     * @var [type]
     */
    public $dispatcher;

    public function __construct(Container $container, GroupCountBasedDispather $dispatcher)
    {
        $this->container  = $container;
        $this->dispatcher = $dispatcher;
    }

    public function process(ServerRequestInterface $psr_request, RequestHandlerInterface $handler): ResponseInterface
    {

        $http_method = $psr_request->getMethod();
        $uri         = $psr_request->getUri()->getPath();

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($http_method, $uri);
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                // ... 405 Method Not Allowed
                break;
            case \FastRoute\Dispatcher::FOUND:

                $controller_class_name = $routeInfo[1];
                $controller            = $this->container->make($controller_class_name);

                if (!$controller instanceof RequestHandlerInterface) {
                    throw new Exception($controller . ' not implements ' . RequestHandlerInterface::class);
                }

                $vars         = $routeInfo[2];
                $psr_request  = $psr_request->withAttribute('routeParams', $vars);
                $psr_response = $controller->handle($psr_request);

                return $psr_response;
        }

    }
}

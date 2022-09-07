<?php

namespace App\ServiceProvider\Basic\HandlerService;

// use App\Http\Middleware\ExampleMiddleware;
use App\Http\Middleware\ParseBodyMiddleware;
use App\Http\Middleware\RouteMiddleware;
use App\ServiceProvider\Basic\HandlerService\ServerRequestFactory;
use Illuminate\Contracts\Container\Container;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Stratigility\MiddlewarePipe;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Handler
{
    /**
     * 服务容器
     *
     * @var [type]
     */
    public $container;

    /**
     * 中间件管道
     *
     * @var [type]
     */
    public $pipe;

    public function __construct(Container $container, MiddlewarePipe $pipe)
    {
        $this->container = $container;
        $this->pipe      = $pipe;
    }

    public function __invoke(Request $swoole_request, Response $swoole_response)
    {
        try {
            if ('/favicon.ico' == $swoole_request->server['path_info'] || '/favicon.ico' == $swoole_request->server['request_uri']) {
                $swoole_response->end();
                return;
            }

            $this->pipe->pipe($this->container->make(ParseBodyMiddleware::class));
            $this->pipe->pipe($this->container->make(RouteMiddleware::class));

            $psr_request  = $this->getPsrRequest($swoole_request);
            $psr_response = $this->pipe->handle($psr_request);

        } catch (\Throwable $th) {
            $psr_response = new JsonResponse(['code' => $th->getCode(), 'message' => $th->getMessage()]);
        } finally {
            $this->pipe->clear();
        }

        $this->emit($psr_response, $swoole_response);
    }

    public function getPsrRequest($swoole_request)
    {
        $server_variables       = $this->getStdServerVariables($swoole_request);
        $server_request_factory = new ServerRequestFactory();

        $request = $server_request_factory->fromGlobals(
            $server_variables,
            $swoole_request->get ?? [],
            $swoole_request->post ?? [],
            $swoole_request->cookie ?? [],
            $swoole_request->files ?? [],
            $swoole_request->getContent()
        );

        return $request;
    }

    public function getStdServerVariables($swoole_request)
    {
        $params_server = $this->getPsrRequestServer($swoole_request);
        $params_header = $this->getPsrRequestHeaders($swoole_request);

        $server_variables = array_merge($params_header, $params_server);

        if (isset($server_variables['REQUEST_URI'], $server_variables['QUERY_STRING']) &&
            strlen($server_variables['QUERY_STRING']) > 0 &&
            !str_contains($server_variables['REQUEST_URI'], '?')) {
            $server_variables['REQUEST_URI'] .= '?' . $server_variables['QUERY_STRING'];
        }

        if (array_key_exists('HTTP_CONTENT_LENGTH', $server_variables)) {
            $server_variables['CONTENT_LENGTH'] = $server_variables['HTTP_CONTENT_LENGTH'];
        }

        if (array_key_exists('HTTP_CONTENT_TYPE', $server_variables)) {
            $server_variables['CONTENT_TYPE'] = $server_variables['HTTP_CONTENT_TYPE'];
        }

        return $server_variables;
    }

    public function getPsrRequestServer($swoole_request)
    {
        $server     = $swoole_request->server ?? [];
        $std_server = array_change_key_case($server, CASE_UPPER);

        return $std_server;
    }

    public function getPsrRequestHeaders($swoole_request)
    {
        $std_headers = [];
        $headers     = $swoole_request->header ?? [];

        foreach ($headers as $key => $value) {

            $key = strtoupper(str_replace('-', '_', $key));
            if (!in_array($key, ['HTTPS', 'REMOTE_ADDR', 'SERVER_PORT'])) {
                $key = 'HTTP_' . $key;
            }
            $std_headers[$key] = $value;
        }
        return $std_headers;
    }

    protected function emit(\Psr\Http\Message\ResponseInterface $psr_response, \Swoole\Http\Response $swoole_response)
    {

        // status
        $swoole_response->status($psr_response->getStatusCode());

        // headers
        foreach ($psr_response->getHeaders() as $key => $val) {
            $val = is_array($val) ? current($val) : $val;
            $swoole_response->header($key, $val);
        }

        $swoole_response->end($psr_response->getBody());
    }
}

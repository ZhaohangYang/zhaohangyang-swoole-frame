<?php

namespace App\Http\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BasicController implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $routeParams = $request->getAttribute('routeParams');
            $action      = $routeParams['action'];

            if (!method_exists($this, $action)) {
                throw new \Exception("404 not find", 1);
            }

            $parse_params = $request->getParsedBody();
            $query_params = $request->getQueryParams();
            $response     = $this->$action($parse_params, $query_params);

            $response = ['code' => '0', 'message' => '伙伴中间件已接收到你的请求', 'data' => $response ?? []];

        } catch (\Throwable $th) {

            $response = ['code' => '500', 'message' => $th->getMessage(), 'data' => []];
        }

        return new JsonResponse($response);
    }

}

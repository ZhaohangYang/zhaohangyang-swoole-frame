<?php

return function (\FastRoute\RouteCollector $route) {
    $route->addRoute('GET', '/api/index/{action}', \App\Http\Controller\IndexController::class);
};

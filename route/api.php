<?php

return function (\FastRoute\RouteCollector $route) {
    $route->addRoute('GET', '/api/index', \App\Http\Controller\IndexController::class);
};

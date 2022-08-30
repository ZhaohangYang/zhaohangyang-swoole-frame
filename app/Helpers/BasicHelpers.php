<?php
namespace App\Helpers;

use App\Application;

if (!function_exists('app')) {
    function app()
    {
        return Application::getContainer()->get('app');
    }
}

if (!function_exists('container')) {
    function container()
    {
        return Application::getContainer();
    }
}

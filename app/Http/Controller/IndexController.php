<?php

namespace App\Http\Controller;

use App\Http\Controller\BasicController;
use App\Models\HuobanOpenApi\HuobanOpenApiBasic;

class IndexController extends BasicController
{
    public function test($params)
    {
        $item = HuobanOpenApiBasic::$huobanItem->get(2300008254243955);
        return $item;
    }
}

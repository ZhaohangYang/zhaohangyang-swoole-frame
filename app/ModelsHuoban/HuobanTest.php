<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ModelsHuoban;

class HuobanTest
{

    public static $var1 = '';
    public $var2        = 'a';

    public function __construct()
    {
    }

    public static function setVar1($var)
    {
        self::$var1 = $var;
    }

    public function setVar2($var)
    {
        $this->var2 = $var;
    }
}

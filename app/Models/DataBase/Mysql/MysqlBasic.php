<?php
namespace App\Models\DataBase\Mysql;

use Illuminate\Database\Capsule\Manager as Capsule;

class MysqlBasic
{
    public $capsule;

    public static function enable($mysql_config)
    {
        self::$capsule = new Capsule;
        self::$capsule->addConnection([
            'driver'      => $mysql_config['driver'],
            'host'        => $mysql_config['host'],
            'database'    => $mysql_config['database'],
            'username'    => $mysql_config['username'],
            'password'    => $mysql_config['password'],
            'charset'     => $mysql_config['charset'],
            'collat​​ion' => $mysql_config['collat​​ion'],
            'prefix'      => $mysql_config['prefix'],
        ]);
        // 设置全局静态可访问DB
        self::$capsule->setAsGlobal();
    }

}

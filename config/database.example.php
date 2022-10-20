
<?php
return [
    'redis' => [
        'enable'   => true,

        'ip'       => '127.0.0.1',
        'port'     => 6379,
        'password' => '',
        'db_index' => 0,
        'time_out' => 1,
        'number'   => 5,
    ],
    'mysql' => [
        'enable'      => false,

        'driver'      => 'mysql',
        'host'        => 'localhost',
        'database'    => 'test',
        'username'    => 'test',
        'password'    => '123',
        'charset'     => 'utf8',
        'collat​​ion' => 'utf8_unicode_ci',
        'prefix'      => '',
    ],
];
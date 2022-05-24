
<?php
return [
    'server'    => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'ssl'  => false,
    ],
    'coroutine' => [
        'options' => [
            // max_coroutine    -    设置全局最大协程数，超过限制后底层将无法创建新的协程，Server 下会被 server->max_coroutine 覆盖。
            // stack_size    -    设置单个协程初始栈的内存尺寸，默认为 2M
            // log_level    v4.0.0    日志等级 详见
            // trace_flags    v4.0.0    跟踪标签 详见
            // socket_connect_timeout    v4.2.10    建立连接超时时间，参考客户端超时规则
            // socket_read_timeout    v4.3.0    读超时，参考客户端超时规则
            // socket_write_timeout    v4.3.0    写超时，参考客户端超时规则
            // socket_dns_timeout    v4.4.0    域名解析超时，参考客户端超时规则
            // socket_timeout    v4.2.10    发送 / 接收超时，参考客户端超时规则
            // dns_cache_expire    v4.2.11    设置 swoole dns 缓存失效时间，单位秒，默认 60 秒
            // dns_cache_capacity    v4.2.11    设置 swoole dns 缓存容量，默认 1000
            // hook_flags    v4.4.0    一键协程化的 hook 范围配置，参考一键协程化
            'hook_flags' => SWOOLE_HOOK_ALL,
            // enable_preemptive_scheduler    v4.4.0    设置打开协程抢占式调度，协程最大执行时间为 10ms，会覆盖 ini 配置
            // dns_server    v4.5.0    设置 dns 查询的 server，默认 "8.8.8.8"
            // exit_condition    v4.5.0    传入一个 callable，返回 bool，可自定义 reactor 退出的条件。如：我希望协程数量等于 0 时程序才退出，则可写 Co::set(['exit_condition' => function () {return Co::stats()['coroutine_num'] === 0;}]);
            // enable_deadlock_check    v4.6.0    设置是否开启协程死锁检测，默认开启
            // deadlock_check_disable_trace    v4.6.0    设置是否输出协程死锁检测的堆栈帧
            // deadlock_check_limit    v4.6.0    限制协程死锁检测时最大输出数
            // deadlock_check_depth    v4.6.0    限制协程死锁检测时返回堆栈帧的数量
            // max_concurrency    v4.8.2    最大并发请求数量
        ],
    ],
];
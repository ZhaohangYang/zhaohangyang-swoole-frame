
<?php
return [
    'huoban_pass' => [
        // 应用信息
        'application_id'     => 1000000,
        'application_secret' => '',
        // 配置名称
        'name'               => 'huoban_pass',
        // 是否启用别名模式
        'alias_model'        => true,
        // 权限类别  enterprise/table
        'app_type'           => 'enterprise',
        // 工作区id
        'space_id'           => '',
        // pass默认地址，切换本地化部署需要修改
        'urls'               => [
            'api'    => 'https://api.huoban.com',
            'upload' => 'https://upload.huoban.com',
            'bi'     => 'https://bi.huoban.com',
        ],
        // 缓存文件存放地址
        'cache_path'         => __DIR__ . '/../storage/huoban/',
    ],
];
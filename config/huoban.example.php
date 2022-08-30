
<?php
return [
    'space_one' => [
        // 应用信息
        'application_id'     => 100000,
        'application_secret' => '',
        // 工作区id
        'space_id'           => '4000000002891045',
        // 配置名称
        'name'               => 'space_one',
        // 是否启用别名模式
        'alias_model'        => true,
        // 权限类别  enterprise/table
        'app_type'           => 'enterprise',

        // pass默认地址，切换本地化部署需要修改
        'urls'               => [
            'api'    => 'https://api.huoban.com',
            'upload' => 'https://upload.huoban.com',
            'bi'     => 'https://bi.huoban.com',
        ],
    ],
];
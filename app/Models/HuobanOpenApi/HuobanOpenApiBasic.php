<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */

namespace App\Models\HuobanOpenApi;

use App\Application;
use HuobanOpenapi\HuobanOpenapi;

/**
 * 基础服务，巡店管理系统
 */
class HuobanOpenApiBasic
{
    public static $huobanConfig;

    public static $huoban;
    public static $huobanItem;

    public static $logPath;

    public static function enable($huoban_config)
    {
        self::$huobanConfig = $huoban_config;

        self::$huoban     = new HuobanOpenapi(self::$huobanConfig);
        self::$huobanItem = self::$huoban->make('item');
    }

    public static function getLogPath()
    {
        $storage_path = Application::StoragePath();
        $date         = date('Y-m-d', strtotime('now'));

        self::$logPath = $storage_path . DIRECTORY_SEPARATOR . 'huoban' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $date;
        is_dir(self::$logPath) || mkdir(self::$logPath, 0777, true);

        return self::$logPath;
    }

    /**
     * 效验伙伴请求返回结果
     *
     * @param [type] $response
     * @param string $location
     * @return void
     */
    public static function verifyHuobanResponse($response, $location = '', $type = 'throw', $supplementary = '')
    {
        if (isset($response['code'])) {
            $message = $response['message'] ?? '未知错误信息';
            $message .= PHP_EOL . $supplementary;

            if ('log' == $type) {
                self::error($location . PHP_EOL . $message);
            } else {
                throw new \Exception($location . $message, 10001);
            }
        }
    }

    public static function error($message)
    {
        self::log($message, 'ERROR');
    }

    public static function info($message)
    {
        self::log($message, 'INFO');
    }

    public static function log($message, $type = null, $log_name = 'main')
    {
        $log_path = self::getLogPath();
        $log_file = $log_path . DIRECTORY_SEPARATOR . $log_name . '.log';

        is_file($log_file) || touch($log_file, 0777, true);

        $date_time = date('Y-m-d H:i:s', time());
        $message   = '[' . $date_time . ' ' . $type . ':]' . $message . PHP_EOL;

        file_put_contents($log_file, $message, FILE_APPEND);
    }

}

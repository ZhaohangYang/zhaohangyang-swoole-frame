<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */

namespace App\Models\Huoban;

use App\Application;
use Huoban\Huoban;

/**
 * 基础服务，巡店管理系统
 */
class HuobanBasic
{
    public static $huobanConfig;

    public static $huoban;
    public static $huobanItem;

    public static $logPath;

    public static function enable($huoban_config, $huoban_persistence_enable = false)
    {
        self::$huobanConfig = $huoban_config;
        self::refresh(true);

        $huoban_persistence_enable && self::timer();
    }

    public static function refresh($init = false)
    {
        self::refreshLogPath($init);
        self::refreshHuoban();
        self::refreshHuobanItem();
    }

    public static function refreshLogPath($init)
    {
        $storage_path = Application::StoragePath();
        $date         = $init ? date('Y-m-d', strtotime('now')) : date('Y-m-d', strtotime('tomorrow'));

        self::$logPath = $storage_path . DIRECTORY_SEPARATOR . 'huoban' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $date;

        is_dir(self::$logPath) || mkdir(self::$logPath, 0777, true);
    }

    public static function refreshHuoban()
    {
        self::$huoban = new Huoban(self::$huobanConfig);
    }

    public static function refreshHuobanItem()
    {
        self::$huobanItem = self::$huoban->make('item');
    }

    public static function timer()
    {
        \Swoole\Timer::tick(3600000, function (int $timer_id) {
            if (date('H', time()) == 23) {
                self::refresh();
            }
        });
    }

    public static function huobanItem()
    {
        return self::$huobanItem;
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
        $log_file = self::$logPath . DIRECTORY_SEPARATOR . $log_name . '.log';
        is_file($log_file) || touch($log_file, 0777, true);

        $date_time = date('Y-m-d H:i:s', time());
        $message   = '[' . $date_time . ' ' . $type . ':]' . $message . PHP_EOL;

        file_put_contents($log_file, $message, FILE_APPEND);
    }

    /**
     * 收集错误信息到伙伴
     *
     * @param [type] $item_id
     * @param [type] $field_key
     * @param [type] $field_value
     * @return void
     */
    public static function collectError($item_id, $field_key, $field_value)
    {
        if ($item_id) {
            $body = [
                'fields' => [
                    $field_key => $field_value,
                ],
            ];
            self::$huobanItem->update($item_id, $body);
        }
    }
}

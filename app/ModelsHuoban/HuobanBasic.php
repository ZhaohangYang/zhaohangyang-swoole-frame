<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ModelsHuoban;

use Huoban\Huoban;

class HuobanBasic
{
    public static $huobanConfig;
    public static $huoban;
    public static $huobanItem;

    public static function init($huoban_config)
    {
        self::$huobanConfig = $huoban_config;
        self::verifyHuobanConfig();
        self::initLogPath();
        self::refreshHuoban($huoban_config);
    }

    public static function verifyHuobanConfig()
    {

        foreach (self::$huobanConfig as $key => $config) {
            if (!$config) {
                throw new \Exception($key . "/" . $config . "--参数异常", 1);
            }
        }
    }

    public static function initLogPath()
    {
        $date     = date('Y-m-d', strtotime('today'));
        $log_path = self::getLogPath($date);

        is_dir(dirname($log_path)) || mkdir($log_path, 0777, true);
        is_file($log_path) || touch($log_path, 0777, true);
    }

    public static function refresh($huoban_config, $timer_id = null)
    {
        self::refreshLogPath($huoban_config);
        self::refreshHuoban($huoban_config);
    }

    public static function refreshHuoban($huoban_config)
    {
        self::$huoban     = new Huoban($huoban_config);
        self::$huobanItem = self::$huoban->make('item');
    }

    public static function huobanItem()
    {
        return self::$huoban->make('item');
    }

    public static function refreshLogPath()
    {
        $date     = date('Y-m-d', strtotime('tomorrow'));
        $log_path = self::getLogPath($date);
        is_file($log_path) || touch($log_path, 0777, true);
    }

    public static function getLogPath($date)
    {
        $log_path = self::$huobanConfig['cache_path'] . 'logs' . DIRECTORY_SEPARATOR . $date . '.log';
        return $log_path;
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

    public static function error($message)
    {
        self::log($message, 'ERROR');
    }

    public static function info($message)
    {
        self::log($message, 'INFO');
    }

    public static function log($message, $type = null)
    {
        $time      = time();
        $date      = date('Y-m-d', $time);
        $date_time = date('Y-m-d H:i:s', $time);

        $message = '[' . $date_time . ' ' . $type . ':]' . $message . PHP_EOL;
        file_put_contents(self::getLogPath($date), $message, FILE_APPEND);
    }
}

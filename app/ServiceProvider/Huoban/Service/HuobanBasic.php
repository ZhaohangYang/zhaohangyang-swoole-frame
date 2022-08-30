<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */

namespace App\ServiceProvider\Huoban\Service;

use App\Application;
use Huoban\Huoban;

class HuobanBasic
{
    public $huobanConfig;

    public $huoban;
    public $huobanItem;

    public $logPath;

    public function __construct($huoban_config)
    {
        $this->huobanConfig = $huoban_config;

        $this->refresh(true);
        $this->timer();
    }

    public function refresh($init = false)
    {
        $this->refreshLogPath($init);
        $this->refreshHuoban();
        $this->refreshHuobanItem();
    }

    public function refreshLogPath($init)
    {
        $storage_path = Application::StoragePath();
        $date         = $init ? date('Y-m-d', strtotime('now')) : date('Y-m-d', strtotime('tomorrow'));

        $this->logPath = $storage_path . DIRECTORY_SEPARATOR . 'huoban' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $date;

        is_dir($this->logPath) || mkdir($this->logPath, 0777, true);
    }

    public function refreshHuoban()
    {
        $this->huoban = new Huoban($this->huobanConfig);
    }

    public function refreshHuobanItem()
    {
        $this->huobanItem = $this->huoban->make('item');
    }

    public function timer()
    {
        \Swoole\Timer::tick(3600000, function (int $timer_id) {
            if (date('H', time()) == 23) {
                $this->refresh();
            }
        });
    }

    /**
     * 效验伙伴请求返回结果
     *
     * @param [type] $response
     * @param string $location
     * @return void
     */
    public function verifyHuobanResponse($response, $location = '', $type = 'throw', $supplementary = '')
    {
        if (isset($response['code'])) {
            $message = $response['message'] ?? '未知错误信息';
            $message .= PHP_EOL . $supplementary;

            if ('log' == $type) {
                $this->error($location . PHP_EOL . $message);
            } else {
                throw new \Exception($location . $message, 10001);
            }
        }
    }

    public function error($message)
    {
        $this->log($message, 'ERROR');
    }

    public function info($message)
    {
        $this->log($message, 'INFO');
    }

    public function log($message, $type = null, $log_name = 'main')
    {
        $log_file = $this->logPath . DIRECTORY_SEPARATOR . $log_name . '.log';
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
    public function collectError($item_id, $field_key, $field_value)
    {
        if ($item_id) {
            $body = [
                'fields' => [
                    $field_key => $field_value,
                ],
            ];
            $this->huobanItem->update($item_id, $body);
        }
    }
}

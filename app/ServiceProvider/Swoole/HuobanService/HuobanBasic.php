<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */

namespace App\ServiceProvider\Swoole\HuobanService;

use App\ServiceProvider\Swoole\DataBaseService\RedisBasic;
use Huoban\Huoban;

/**
 * 基础服务，巡店管理系统
 */
class HuobanBasic
{
    public static $huobanConfig;
    public static $huoban;
    public static $expire = 24 * 60 * 60;

    public static function enable($huoban_config, $huoban_persistence_enable = false)
    {
        self::$huobanConfig = $huoban_config;
        self::refresh();

        $huoban_persistence_enable && self::timer();
    }

    public static function timer()
    {
        \Swoole\Timer::tick(self::$expire, function (int $timer_id) {
            self::refresh();
        });
    }

    public static function refresh()
    {
        self::refreshHuoban();
    }

    public static function refreshHuoban()
    {
        self::$huoban = new Huoban(self::$huobanConfig);

        $ticket = self::$huoban->getTicket();
        self::setCacheTicket($ticket);
    }

    public static function getCacheTicketHkey()
    {
        return self::$huobanConfig['application_id'] . '_ticket';
    }

    public static function setCacheTicket($ticket, $expire_time = null)
    {
        $key  = self::getCacheTicketHkey();
        $body = [
            'ticket'      => $ticket,
            'expire_time' => $expire_time ?: (time() + self::$expire - 10),
        ];
        RedisBasic::hmset($key, $body);
    }

    public static function getCacheTicket()
    {
        $key = self::getCacheTicketHkey();

        $ticket_json = RedisBasic::hgetall($key) ?: '';
        $ticket_data = json_decode($ticket_json, true);

        $ticket      = $ticket_data['ticket'] ?? '';
        $expire_time = $ticket_data['expire_time'] ?? '';

        if ($ticket && $expire_time && time() < $expire_time) {
            return $ticket;
        }

        return null;
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

            throw new \Exception($location . $message, 10001);
        }
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
            self::$huoban->make('item')->update($item_id, $body);
        }
    }
}

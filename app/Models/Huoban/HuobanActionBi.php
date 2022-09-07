<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */

namespace App\Models\Huoban;

use Swoole\Coroutine;
use Swoole\Coroutine\WaitGroup;

abstract class HuobanActionBi
{
    public $tableId       = '';
    public $tableAlias    = '';
    public $preTableAlias = '';
    public $huobanModels  = [];
    // 映射请求并发
    public $mappingBasicConcurrent = 20;
    // 映射请求limit
    public $mappingBasicLimit = 500;

    public function mappingBasic($mappingBasicCallbacks, $body = [])
    {
        $filtered = HuobanBasic::$huoban->make('BiItem')->getFiltered($this->tableId, $body);

        $page_size  = $this->mappingBasicLimit;
        $page_total = ceil($filtered / $page_size);

        $page_total_arr = range(0, $page_total);
        $page_blocks    = array_chunk($page_total_arr, $this->mappingBasicConcurrent);

        foreach ($page_blocks as $page_block) {

            $wg = new WaitGroup();
            foreach ($page_block as $page) {

                $wg->add();

                Coroutine::create(function () use ($page, $page_size, $mappingBasicCallbacks, $wg) {
                    $body = [
                        'offset' => $page * $page_size,
                        'limit'  => $page_size,
                    ];
                    $response = HuobanBasic::$huoban->make('BiItem')->findFormatItems($this->tableId, $body);

                    foreach ($mappingBasicCallbacks as $mappingBasicCallback) {
                        $this->$mappingBasicCallback($response);
                    }

                    print_r($this->preTableAlias . ':page-' . $page . '已完成' . PHP_EOL);

                    $wg->done();
                });
            }

            $wg->wait();
        }
    }

    public function make($model_name)
    {
        if (!isset($this->huobanModels[$model_name])) {
            $model                           = '\\App\\ModelsHuoban\\' . ucfirst($model_name);
            $this->huobanModels[$model_name] = new $model();
        }

        return $this->huobanModels[$model_name];
    }

    public function getForItemId($item_id)
    {
        $response = HuobanBasic::huobanItem()->getFormatItem($item_id);
        HuobanBasic::verifyHuobanResponse($response, __METHOD__);

        return $response;
    }

    /**
     * TODO待验证后放入数组的是否被去除
     * 用于一对多存储缓存数据排重功能
     * @param $_2d_array
     * @param $unique_key
     * @return mixed
     * @author 14714
     * @date 2022-07-13 16:53
     */
    public function unique_multidim_array($_2d_array, $unique_key)
    {
        $tmp_key[] = array();
        foreach ($_2d_array as $key => &$item) {
            if (is_array($item) && isset($item[$unique_key])) {
                if (in_array($item[$unique_key], $tmp_key)) {
                    unset($_2d_array[$key]);
                } else {
                    $tmp_key[] = $item[$unique_key];
                }
            }
        }
        //重置一下二维数组的索引
        return array_slice($_2d_array, 0, count($_2d_array), false);
    }
}

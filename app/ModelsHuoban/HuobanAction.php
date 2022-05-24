<?php
/*
 * @Author: ZhaohangYang <yangzhaohang@comsenz-service.com>
 * @Date: 2021-06-23 16:58:47
 * @Description: 伙伴智慧大客户研发部
 */
namespace App\ModelsHuoban;

use Swoole\Coroutine;
use Swoole\Coroutine\WaitGroup;

abstract class HuobanAction
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
        $total = HuobanBasic::huobanItem()->getTotal($this->tableId, $body);

        $page_size  = $this->mappingBasicLimit;
        $page_total = ceil($total / $page_size);

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
                    $response = HuobanBasic::huobanItem()->findFormatItems($this->tableId, $body);
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
}

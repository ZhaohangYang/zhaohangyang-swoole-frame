<?php
namespace App\Models\DataBase\Mysql;

use App\Models\DataBase\DbBasic;
use Illuminate\Database\Capsule\Manager as Capsule;

class MysqlExample extends DbBasic
{
    public function getOneDayDate()
    {
        $start_time = date('Y-m-d H:i:00', time() - 3600 * 1);
        $end_time   = date('Y-m-d H:i:00');
        $response   = Capsule::table('test')
            ->select('id')
            ->where('updated_at', '>=', $start_time)
            ->where('updated_at', '<', $end_time)->get()->toArray();

        return $response;
    }

}

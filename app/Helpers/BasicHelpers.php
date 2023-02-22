<?php
namespace App\Helpers;

class BasicHelpers
{
    public static function printFormatMessage($message, $type = 'INFO')
    {
        $data_time = date('Y-m-d H:i:s');
        return "{$type}::{$data_time} >> {$message}" . PHP_EOL;
    }
}

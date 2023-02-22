<?php
namespace App\Helpers;

class BasicHelpers
{
    public static function FormatMessage($message, $type = 'INFO')
    {
        $data_time = date('Y-m-d H:i:s');
        return "{$type}::{$data_time} >> {$message}" . PHP_EOL;
    }
    public static function printFormatMessage($message, $type = 'INFO')
    {
        $message = self::FormatMessage($message, $type);
        print_r($message);
    }

    public static function vardumpFormatMessage($message, $type = 'INFO')
    {
        $message = self::FormatMessage($message, $type);
        var_dump($message);
    }
}
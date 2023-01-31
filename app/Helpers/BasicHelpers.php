<?php
namespace App\Helpers;

class BasicHelpers
{
    public static function printFormatMessage($message)
    {
        print_r(date('y-m-d H:i:s') . $message . PHP_EOL);
    }
}

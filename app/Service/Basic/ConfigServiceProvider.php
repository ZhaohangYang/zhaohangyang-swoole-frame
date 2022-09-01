<?php
namespace App\Service\Basic;

use App\Service\BasicServiceProvider;
use App\Service\Basic\Provider\Config;
use Illuminate\Contracts\Config\Repository;

class ConfigServiceProvider extends BasicServiceProvider
{

    public function register()
    {
        $config_path = $this->container->get('app')::configPath();
        $config      = self::getConfig($config_path);

        $this->container->singleton('config', function () use ($config) {
            return new Config($config);
        });

        // 标注配置文件是由什么契约文件生成的
        $this->container->alias('config', Repository::class);
    }

    /**
     * 根据传入配置文件路径，返回一个配置文件内容的聚合数组，一维数组的key即为配置文件的文件名
     *
     * @param [type] $config_path
     * @return array
     */
    public static function getConfig($config_path): array
    {
        $config       = [];
        $config_files = glob($config_path . '*');

        array_walk($config_files, function ($config_file) use (&$config) {

            $config_file_name = basename($config_file);

            $config_name          = str_replace(strrchr($config_file_name, "."), "", $config_file_name);
            $config[$config_name] = include $config_file;
        });

        return $config;
    }
}

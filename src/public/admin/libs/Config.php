<?php
namespace stcms;

class Config
{
    private static $config = array();
    private static $loaded = false;

    private static function load()
    {
        if (self::$loaded) {
            return;
        }

        $configPath = __DIR__ . '/../config.php';
        if (file_exists($configPath)) {
            self::$config = require $configPath;
        } else {
            self::$config = self::getDefaultConfig();
        }
        self::$loaded = true;
    }

    private static function getDefaultConfig()
    {
        return array(
            'paths' => array(
                'data_dir' => __DIR__ . '/../.data',
                'schemas_file' => __DIR__ . '/../schemas.json',
            ),
            'timezone' => array(
                'default' => 'UTC',
            ),
        );
    }

    public static function get($key, $default = null)
    {
        self::load();
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    public static function getDataDirPath()
    {
        return self::get('paths.data_dir', __DIR__ . '/../.data');
    }

    public static function getSchemasFilePath()
    {
        return self::get('paths.schemas_file', __DIR__ . '/../schemas.json');
    }

    public static function initTimezone()
    {
        $timezone = self::get('timezone.default', 'UTC');
        date_default_timezone_set($timezone);
    }
}
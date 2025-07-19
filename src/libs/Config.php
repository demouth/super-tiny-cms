<?php
namespace stcms;

class Config
{
    private static array $config = [];
    private static bool $loaded = false;

    private static function load(): void
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

    private static function getDefaultConfig(): array
    {
        return [
            'paths' => [
                'data_dir' => __DIR__ . '/../.data',
                'schemas_file' => __DIR__ . '/../schemas.json',
            ],
        ];
    }

    public static function get(string $key, $default = null)
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

    public static function getDataDirPath(): string
    {
        return self::get('paths.data_dir', __DIR__ . '/../.data');
    }

    public static function getSchemasFilePath(): string
    {
        return self::get('paths.schemas_file', __DIR__ . '/../schemas.json');
    }
}
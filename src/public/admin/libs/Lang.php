<?php
namespace stcms;

class Lang
{
    private static $translations = array();
    private static $currentLang = 'en';
    private static $loaded = false;

    public static function init($lang = null)
    {
        if ($lang) {
            self::$currentLang = $lang;
        } else {
            self::detectLanguage();
        }
        self::loadTranslations();
    }

    private static function detectLanguage()
    {
        $configLang = Config::get('language.default', 'en');
        
        if ($configLang && $configLang !== 'auto') {
            self::$currentLang = $configLang;
            return;
        }

        $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        if (strpos($acceptLanguage, 'ja') !== false) {
            self::$currentLang = 'ja';
        } else {
            self::$currentLang = 'en';
        }
    }

    private static function loadTranslations()
    {
        if (self::$loaded) {
            return;
        }

        $langFile = __DIR__ . '/../langs/' . self::$currentLang . '.php';
        if (file_exists($langFile)) {
            self::$translations = require $langFile;
        } else {
            $fallbackFile = __DIR__ . '/../langs/en.php';
            if (file_exists($fallbackFile)) {
                self::$translations = require $fallbackFile;
            }
        }
        
        self::$loaded = true;
    }

    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::init();
        }

        return isset(self::$translations[$key]) ? self::$translations[$key] : ($default !== null ? $default : $key);
    }

    public static function getCurrentLang()
    {
        return self::$currentLang;
    }

    public static function setLang($lang)
    {
        self::$currentLang = $lang;
        self::$loaded = false;
        self::loadTranslations();
    }
}
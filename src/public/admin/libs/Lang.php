<?php
namespace stcms;

class Lang
{
    private static array $translations = [];
    private static string $currentLang = 'en';
    private static bool $loaded = false;

    public static function init(?string $lang = null): void
    {
        if ($lang) {
            self::$currentLang = $lang;
        } else {
            self::detectLanguage();
        }
        self::loadTranslations();
    }

    private static function detectLanguage(): void
    {
        $configLang = Config::get('language.default', 'en');
        
        if ($configLang && $configLang !== 'auto') {
            self::$currentLang = $configLang;
            return;
        }

        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if (strpos($acceptLanguage, 'ja') !== false) {
            self::$currentLang = 'ja';
        } else {
            self::$currentLang = 'en';
        }
    }

    private static function loadTranslations(): void
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

    public static function get(string $key, ?string $default = null): string
    {
        if (!self::$loaded) {
            self::init();
        }

        return self::$translations[$key] ?? $default ?? $key;
    }

    public static function getCurrentLang(): string
    {
        return self::$currentLang;
    }

    public static function setLang(string $lang): void
    {
        self::$currentLang = $lang;
        self::$loaded = false;
        self::loadTranslations();
    }
}
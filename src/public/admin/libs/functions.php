<?php
require_once __DIR__.'/Lang.php';

use stcms\Lang;

if (!function_exists('_h')) {
    function _h($in) {
        return htmlspecialchars($in, ENT_QUOTES);
    }
}

if (!function_exists('_t')) {
    function _t(string $key, ?string $default = null): string {
        return Lang::get($key, $default);
    }
}

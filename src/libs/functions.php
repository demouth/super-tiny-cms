<?php
if (!function_exists('_h')) {
    function _h($in) {
        return htmlspecialchars($in, ENT_QUOTES);
    }
}

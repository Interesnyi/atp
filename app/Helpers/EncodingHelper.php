<?php

namespace App\Helpers;

class EncodingHelper {
    /**
     * Устанавливает правильные заголовки для кодировки UTF-8
     */
    public static function setUtf8Headers() {
        if (!headers_sent()) {
            ini_set('default_charset', 'UTF-8');
            header('Content-Type: text/html; charset=UTF-8');
            mb_internal_encoding('UTF-8');
            mb_http_output('UTF-8');
        }
    }
} 
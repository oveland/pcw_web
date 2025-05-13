<?php

namespace App\Helpers;

class CountPatch
{
    public static function register()
    {
        // Solo aplica el parche si estamos en PHP 7.2+
        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
            // Añade un interceptor para la función count()
            require_once __DIR__ . '/CountFunction.php';
        }
    }
}
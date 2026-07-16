<?php
require_once __DIR__ . '/../../inc/config.php';

// Evitar duplicado de función
if (!function_exists('assetUrl')) {
    function assetUrl($path) {
        if (preg_match('/^(https?:|\/)/', $path)) {
            return $path;
        }
        return URLBASE . '/' . ltrim($path, '/');
    }
}

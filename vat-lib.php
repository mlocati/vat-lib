<?php

spl_autoload_register(
    function ($class) {
        if (strpos($class, 'VATLib\\') !== 0) {
            return;
        }
        $file = __DIR__ . '/src' . str_replace('\\', '/', substr($class, strlen('VATLib'))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
);

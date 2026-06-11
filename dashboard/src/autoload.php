<?php

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Shared\\' => dirname(__DIR__, 3) . '/src/Shared',
        'Dashboard\\' => __DIR__,
        'App\\' => dirname(__DIR__, 3) . '/app/src',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . '/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

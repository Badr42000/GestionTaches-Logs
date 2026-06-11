<?php

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Shared\\' => dirname(__DIR__, 2) . '/src/Shared',
        'App\\' => __DIR__,
        'Dashboard\\' => dirname(__DIR__, 2) . '/dashboard/src',
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

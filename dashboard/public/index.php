<?php

require_once __DIR__ . '/../src/autoload.php';

set_exception_handler(function (Throwable $e) {
    http_response_code(500);
    echo 'Une erreur interne est survenue.';
    exit;
});

$db = Database::getInstance();
$controller = new DashboardController($db);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/' || $uri === '/index.php') {
    $controller->handleList();
} elseif ($uri === '/tasks') {
    $controller->handleTasks();
} else {
    http_response_code(404);
    echo '404 Not Found';
}

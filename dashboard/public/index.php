<?php

require_once __DIR__ . '/../src/autoload.php';

$db = Database::getInstance();
$controller = new DashboardController($db);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/' || $uri === '/index.php') {
    $controller->handleList();
} else {
    http_response_code(404);
    echo '404 Not Found';
}

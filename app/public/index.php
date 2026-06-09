<?php

session_start();

require_once __DIR__ . '/../src/autoload.php';

$db = Database::getInstance();
$logger = new Logger();
$controller = new TaskController($db, $logger);
$auth = new AuthController($db);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/login' && $method === 'GET') {
    $auth->handleLoginForm();
} elseif ($uri === '/login' && $method === 'POST') {
    $auth->handleLogin();
} elseif ($uri === '/logout') {
    $auth->handleLogout();
} else {
    AuthController::requireAuth();

    if ($uri === '/' || $uri === '/index.php') {
        $controller->handleList();
    } elseif ($uri === '/create' && $method === 'GET') {
        $controller->handleCreateForm();
    } elseif ($uri === '/create' && $method === 'POST') {
        $controller->handleCreate();
    } elseif (preg_match('#^/edit/(\d+)$#', $uri, $m) && $method === 'GET') {
        $controller->handleEditForm((int)$m[1]);
    } elseif (preg_match('#^/edit/(\d+)$#', $uri, $m) && $method === 'POST') {
        $controller->handleEdit((int)$m[1]);
    } elseif (preg_match('#^/delete/(\d+)$#', $uri, $m) && $method === 'POST') {
        $controller->handleDelete((int)$m[1]);
    } elseif (preg_match('#^/status/(\d+)$#', $uri, $m) && $method === 'POST') {
        $controller->handleStatus((int)$m[1]);
    } else {
        http_response_code(404);
        echo '404 Not Found';
    }
}

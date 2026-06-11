<?php

use App\Controller\AuthController;
use App\Controller\TaskController;
use App\Service\SyslogLogger;
use Shared\Core\Database;

session_start();

require_once __DIR__ . '/../src/autoload.php';

set_exception_handler(function (Throwable $e) {
    $logger = new SyslogLogger();
    $logger->send('err', 'tasklogger', json_encode([
        'action' => 'ERROR_UNHANDLED',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]));
    http_response_code(500);
    echo 'Une erreur interne est survenue.';
    exit;
});

$logger = new SyslogLogger();
$controller = new TaskController($logger);
$auth = new AuthController($logger);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/login' && $method === 'GET') {
    $auth->handleLoginForm();
} elseif ($uri === '/login' && $method === 'POST') {
    $auth->handleLogin();
} elseif ($uri === '/register' && $method === 'GET') {
    $auth->handleRegisterForm();
} elseif ($uri === '/register' && $method === 'POST') {
    $auth->handleRegister();
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

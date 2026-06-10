<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\User;
use App\Service\LoggerInterface;

class AuthController
{
    private User $user;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->user = new User(Database::getInstance());
        $this->logger = $logger;
    }

    public function handleLoginForm(): void
    {
        $this->render('login', ['error' => '', 'hideCreate' => true]);
    }

    public function handleLogin(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        if ($username === '' || $password === '') {
            $this->logAuthFailure('Champs vides', $username, $ip);
            $this->render('login', ['error' => 'Identifiants requis.', 'hideCreate' => true]);
            return;
        }

        $userRow = $this->user->findByUsername($username);

        if (!$userRow || !password_verify($password, $userRow['password'])) {
            $this->logAuthFailure('Identifiants incorrects', $username, $ip);
            $this->render('login', ['error' => 'Identifiants incorrects.', 'hideCreate' => true]);
            return;
        }

        $_SESSION['user'] = [
            'id' => (int)$userRow['id'],
            'username' => $userRow['username'],
        ];

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'AUTH_LOGIN_SUCCESS',
            'username' => $userRow['username'],
            'ip' => $ip,
        ]));

        header('Location: /');
        exit;
    }

    public function handleLogout(): void
    {
        $username = $_SESSION['user']['username'] ?? 'unknown';

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'AUTH_LOGOUT',
            'username' => $username,
        ]));

        unset($_SESSION['user']);
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function handleRegisterForm(): void
    {
        $this->render('register', ['error' => '', 'hideCreate' => true]);
    }

    public function handleRegister(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        if ($username === '' || $password === '') {
            $this->logAuthFailure('Champs vides', $username, $ip);
            $this->render('register', ['error' => 'Tous les champs sont requis.', 'hideCreate' => true]);
            return;
        }

        if (strlen($password) < 4) {
            $this->logAuthFailure('Mot de passe trop court', $username, $ip);
            $this->render('register', ['error' => 'Le mot de passe doit faire au moins 4 caractères.', 'hideCreate' => true]);
            return;
        }

        if ($this->user->exists($username)) {
            $this->logAuthFailure('Utilisateur existant', $username, $ip);
            $this->render('register', ['error' => 'Ce nom d\'utilisateur existe déjà.', 'hideCreate' => true]);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id = $this->user->create($username, $hash);

        $_SESSION['user'] = [
            'id' => $id,
            'username' => $username,
        ];

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'AUTH_REGISTER_SUCCESS',
            'username' => $username,
            'ip' => $ip,
        ]));

        header('Location: /');
        exit;
    }

    public static function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    private function logAuthFailure(string $reason, string $username, string $ip): void
    {
        $this->logger->send('warning', 'tasklogger', json_encode([
            'action' => 'AUTH_LOGIN_FAILED',
            'reason' => $reason,
            'username' => $username,
            'ip' => $ip,
        ]));
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../templates/layout.php';
    }
}

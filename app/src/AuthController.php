<?php

class AuthController
{
    private PDO $pdo;
    private Logger $logger;

    public function __construct(PDO $pdo, ?Logger $logger = null)
    {
        $this->pdo = $pdo;
        $this->logger = $logger ?? new Logger();
    }

    public function handleLoginForm(): void
    {
        $this->render('login', ['error' => '', 'hideCreate' => true]);
    }

    public function handleLogin(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->logger->send('warning', 'tasklogger', json_encode([
                'action' => 'AUTH_LOGIN_FAILED',
                'reason' => 'Champs vides',
                'username' => $username,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]));
            $this->render('login', ['error' => 'Identifiants requis.', 'hideCreate' => true]);
            return;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $this->logger->send('warning', 'tasklogger', json_encode([
                'action' => 'AUTH_LOGIN_FAILED',
                'reason' => 'Identifiants incorrects',
                'username' => $username,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]));
            $this->render('login', ['error' => 'Identifiants incorrects.', 'hideCreate' => true]);
            return;
        }

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
        ];

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'AUTH_LOGIN_SUCCESS',
            'username' => $user['username'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
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

        if ($username === '' || $password === '') {
            $this->logger->send('warning', 'tasklogger', json_encode([
                'action' => 'AUTH_REGISTER_FAILED',
                'reason' => 'Champs vides',
                'username' => $username,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]));
            $this->render('register', ['error' => 'Tous les champs sont requis.', 'hideCreate' => true]);
            return;
        }

        if (strlen($password) < 4) {
            $this->logger->send('warning', 'tasklogger', json_encode([
                'action' => 'AUTH_REGISTER_FAILED',
                'reason' => 'Mot de passe trop court',
                'username' => $username,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]));
            $this->render('register', ['error' => 'Le mot de passe doit faire au moins 4 caractères.', 'hideCreate' => true]);
            return;
        }

        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $this->logger->send('warning', 'tasklogger', json_encode([
                'action' => 'AUTH_REGISTER_FAILED',
                'reason' => 'Utilisateur existant',
                'username' => $username,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]));
            $this->render('register', ['error' => 'Ce nom d\'utilisateur existe déjà.', 'hideCreate' => true]);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $hash]);

        $_SESSION['user'] = [
            'id' => (int)$this->pdo->lastInsertId(),
            'username' => $username,
        ];

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'AUTH_REGISTER_SUCCESS',
            'username' => $username,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]));

        header('Location: /');
        exit;
    }

    public static function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            $logger = new Logger();
            $logger->send('warning', 'tasklogger', json_encode([
                'action' => 'SECURITY_ACCESS_DENIED',
                'reason' => 'Utilisateur non authentifié',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]));
            header('Location: /login');
            exit;
        }
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../templates/layout.php';
    }
}

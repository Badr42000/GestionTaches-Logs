<?php

class AuthController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
            $this->render('login', ['error' => 'Identifiants requis.', 'hideCreate' => true]);
            return;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $this->render('login', ['error' => 'Identifiants incorrects.', 'hideCreate' => true]);
            return;
        }

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
        ];

        header('Location: /');
        exit;
    }

    public function handleLogout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
        header('Location: /login');
        exit;
    }

    public static function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
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

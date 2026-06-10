<?php

namespace App\Model;

class User extends AbstractModel
{
    public function findByUsername(string $username): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function exists(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return (bool)$stmt->fetch();
    }

    public function create(string $username, string $passwordHash): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $passwordHash]);
        return (int)$this->pdo->lastInsertId();
    }
}

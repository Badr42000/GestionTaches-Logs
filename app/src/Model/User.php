<?php

namespace App\Model;

use PDOStatement;

class User extends AbstractModel
{
    /** @return array<string, mixed>|false */
    public function findByUsername(string $username): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$username]);
        $result = $stmt->fetch();
        assert(is_array($result) || $result === false);
        /** @var array<string, mixed>|false $result */
        return $result;
    }

    public function exists(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = ?');
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$username]);
        return (bool)$stmt->fetch();
    }

    public function create(string $username, string $passwordHash): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$username, $passwordHash]);
        return (int)$this->pdo->lastInsertId();
    }
}

<?php

namespace App\Model;

use PDOStatement;

class Task extends AbstractModel
{
    /** @return array<int, array<string, mixed>> */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM tasks ORDER BY created_at DESC');
        assert($stmt instanceof PDOStatement);
        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|false */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        assert(is_array($result) || $result === false);
        /** @var array<string, mixed>|false $result */
        return $result;
    }

    public function create(string $title, string $description, string $priority, string $createdBy): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, priority, created_by) VALUES (?, ?, ?, ?)'
        );
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$title, $description, $priority, $createdBy]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $description, string $priority): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = ?, description = ?, priority = ? WHERE id = ?'
        );
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$title, $description, $priority, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$id]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE tasks SET status = ? WHERE id = ?');
        assert($stmt instanceof PDOStatement);
        $stmt->execute([$status, $id]);
    }
}

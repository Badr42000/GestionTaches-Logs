<?php

namespace App\Model;

class Task extends AbstractModel
{
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM tasks ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $title, string $description, string $priority, string $createdBy): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, priority, created_by) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$title, $description, $priority, $createdBy]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $description, string $priority): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = ?, description = ?, priority = ? WHERE id = ?'
        );
        $stmt->execute([$title, $description, $priority, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE tasks SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }
}

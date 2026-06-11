<?php

namespace Dashboard\Model;

use PDOStatement;

class Task extends AbstractModel
{
    /** @return array<int, array<string, mixed>> */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM tasks
             ORDER BY FIELD(priority, 'haute', 'moyenne', 'basse'), created_at DESC"
        );
        assert($stmt instanceof PDOStatement);
        return $stmt->fetchAll();
    }
}

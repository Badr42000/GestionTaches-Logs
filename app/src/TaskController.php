<?php

class TaskController
{
    private PDO $pdo;
    private Logger $logger;

    public function __construct(PDO $pdo, Logger $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function handleList(): void
    {
        $stmt = $this->pdo->query('SELECT * FROM tasks ORDER BY created_at DESC');
        $tasks = $stmt->fetchAll();

        $this->render('list', ['tasks' => $tasks]);
    }

    public function handleCreateForm(): void
    {
        $this->render('form', [
            'task' => ['title' => '', 'description' => '', 'priority' => 'moyenne'],
        ]);
    }

    public function handleCreate(): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'moyenne';
        $username = $_SESSION['user']['username'] ?? 'unknown';

        if ($title === '') {
            $this->render('form', [
                'task' => $_POST,
                'error' => 'Le titre est requis.',
            ]);
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, priority, created_by) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$title, $description, $priority, $username]);
        $id = (int)$this->pdo->lastInsertId();

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TASK_CREATED',
            'id' => $id,
            'title' => $title,
            'priority' => $priority,
            'status' => 'todo',
            'username' => $username,
        ]));

        header('Location: /');
        exit;
    }

    public function handleEditForm(int $id): void
    {
        $task = $this->findTaskOr404($id);

        $this->render('form', ['task' => $task]);
    }

    public function handleEdit(int $id): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'moyenne';
        $username = $_SESSION['user']['username'] ?? 'unknown';

        if ($title === '') {
            $this->render('form', [
                'task' => ['id' => $id, 'title' => $title, 'description' => $description, 'priority' => $priority],
                'error' => 'Le titre est requis.',
            ]);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = ?, description = ?, priority = ? WHERE id = ?'
        );
        $stmt->execute([$title, $description, $priority, $id]);

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TASK_UPDATED',
            'id' => $id,
            'title' => $title,
            'priority' => $priority,
            'username' => $username,
        ]));

        header('Location: /');
        exit;
    }

    public function handleDelete(int $id): void
    {
        $task = $this->findTaskOr404($id);
        $username = $_SESSION['user']['username'] ?? 'unknown';

        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TASK_DELETED',
            'id' => $id,
            'title' => $task['title'],
            'username' => $username,
        ]));

        header('Location: /');
        exit;
    }

    public function handleStatus(int $id): void
    {
        $task = $this->findTaskOr404($id);
        $newStatus = $_POST['status'] ?? '';
        $username = $_SESSION['user']['username'] ?? 'unknown';

        $validStatuses = ['todo', 'in_progress', 'done'];
        if (!in_array($newStatus, $validStatuses, true)) {
            header('Location: /');
            exit;
        }

        $stmt = $this->pdo->prepare('UPDATE tasks SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $id]);

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TASK_UPDATED',
            'id' => $id,
            'field' => 'status',
            'old_value' => $task['status'],
            'new_value' => $newStatus,
            'username' => $username,
        ]));

        header('Location: /');
        exit;
    }

    private function findTaskOr404(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        $task = $stmt->fetch();

        if (!$task) {
            http_response_code(404);
            echo 'Tâche introuvable.';
            exit;
        }

        return $task;
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../templates/layout.php';
    }
}

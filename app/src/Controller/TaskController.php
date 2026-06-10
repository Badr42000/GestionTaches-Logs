<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Task;
use App\Service\LoggerInterface;

class TaskController
{
    private Task $task;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->task = new Task(Database::getInstance());
        $this->logger = $logger;
    }

    public function handleList(): void
    {
        $username = $_SESSION['user']['username'] ?? 'unknown';
        $tasks = $this->task->findAll();

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TASK_LISTED',
            'username' => $username,
            'count' => count($tasks),
        ]));

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

        $id = $this->task->create($title, $description, $priority, $username);

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
        $username = $_SESSION['user']['username'] ?? 'unknown';
        $task = $this->findTaskOr404($id);

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TASK_VIEWED',
            'id' => $id,
            'title' => $task['title'],
            'username' => $username,
        ]));

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

        $this->task->update($id, $title, $description, $priority);

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

        $this->task->delete($id);

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

        $this->task->updateStatus($id, $newStatus);

        $this->logger->send('info', 'tasklogger', json_encode([
            'action' => 'TASK_STATUS_CHANGED',
            'id' => $id,
            'title' => $task['title'],
            'old_value' => $task['status'],
            'new_value' => $newStatus,
            'username' => $username,
        ]));

        header('Location: /');
        exit;
    }

    private function findTaskOr404(int $id): array
    {
        $task = $this->task->findById($id);

        if (!$task) {
            $username = $_SESSION['user']['username'] ?? 'unknown';
            $this->logger->send('warning', 'tasklogger', json_encode([
                'action' => 'SECURITY_RESOURCE_NOT_FOUND',
                'type' => 'task',
                'id' => $id,
                'username' => $username,
            ]));
            http_response_code(404);
            echo 'Tâche introuvable.';
            exit;
        }

        return $task;
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../templates/layout.php';
    }
}

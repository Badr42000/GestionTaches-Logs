<?php

namespace Dashboard\Controller;

use Dashboard\Model\LogEntry;
use Dashboard\Model\Task;
use Shared\Core\Database;

class DashboardController
{
    private LogEntry $logEntry;
    private Task $taskModel;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->logEntry = new LogEntry($pdo);
        $this->taskModel = new Task($pdo);
    }

    public function handleList(): void
    {
        $filter = $_GET['action'] ?? '';
        $category = $_GET['category'] ?? '';

        $logs = $this->logEntry->findAll($filter, $category);
        $stats = $this->logEntry->getStats();

        $this->render('logs', [
            'logs' => $logs,
            'stats' => $stats,
            'current_filter' => $filter,
            'current_category' => $category,
            'view' => 'logs',
        ]);
    }

    public function handleTasks(): void
    {
        $tasks = $this->taskModel->findAll();

        $this->render('tasks', [
            'tasks' => $tasks,
            'view' => 'tasks',
        ]);
    }

    /** @param array<string, mixed> $data */
    private function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../templates/layout.php';
    }
}

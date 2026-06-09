<?php

class DashboardController
{
    private PDO $pdo;

    private const SEVERITY_LABELS = [
        0 => 'emerg', 1 => 'alert', 2 => 'crit', 3 => 'err',
        4 => 'warning', 5 => 'notice', 6 => 'info', 7 => 'debug',
    ];

    private const ACTION_LABELS = [
        'TASK_CREATED' => 'Création',
        'TASK_UPDATED' => 'Modification',
        'TASK_DELETED' => 'Suppression',
    ];

    private const ACTION_ICONS = [
        'TASK_CREATED' => 'circle-plus',
        'TASK_UPDATED' => 'pencil',
        'TASK_DELETED' => 'trash-2',
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleList(): void
    {
        $filter = $_GET['action'] ?? '';

        if ($filter && in_array($filter, ['TASK_CREATED', 'TASK_UPDATED', 'TASK_DELETED'], true)) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:'
                 AND Message LIKE ?
                 ORDER BY ReceivedAt DESC
                 LIMIT 200"
            );
            $stmt->execute(['%"action":"' . $filter . '"%']);
        } else {
            $stmt = $this->pdo->query(
                "SELECT * FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:'
                 ORDER BY ReceivedAt DESC
                 LIMIT 200"
            );
        }

        $logs = $stmt->fetchAll();

        $stats = $this->getStats();

        $this->render('logs', [
            'logs' => $logs,
            'stats' => $stats,
            'current_filter' => $filter,
            'view' => 'logs',
        ]);
    }

    public function handleTasks(): void
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM tasks
             ORDER BY FIELD(priority, 'haute', 'moyenne', 'basse'), created_at DESC"
        );
        $tasks = $stmt->fetchAll();

        $this->render('tasks', [
            'tasks' => $tasks,
            'view' => 'tasks',
        ]);
    }

    private function getStats(): array
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) AS total FROM SystemEvents WHERE SysLogTag = 'tasklogger:'"
        );
        $total = $stmt->fetchColumn();

        $actions = [];
        foreach (['TASK_CREATED', 'TASK_UPDATED', 'TASK_DELETED'] as $action) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:' AND Message LIKE ?"
            );
            $stmt->execute(['%"action":"' . $action . '"%']);
            $actions[$action] = (int)$stmt->fetchColumn();
        }

        return ['total' => (int)$total, 'actions' => $actions];
    }

    private function humanize(array $data): string
    {
        $action = $data['action'] ?? '';

        return match ($action) {
            'TASK_CREATED' => sprintf(
                'Tâche <strong>%s</strong> créée (priorité : %s) par <strong>%s</strong>',
                htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['priority'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            'TASK_UPDATED' => !empty($data['field'])
                ? sprintf(
                    'Tâche #%d : <strong>%s</strong> → <strong>%s</strong> par <strong>%s</strong>',
                    (int)($data['id'] ?? 0),
                    htmlspecialchars($data['old_value'] ?? '', ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($data['new_value'] ?? '', ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
                )
                : sprintf(
                    'Tâche <strong>%s</strong> modifiée par <strong>%s</strong>',
                    htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
                ),
            'TASK_DELETED' => sprintf(
                'Tâche <strong>%s</strong> supprimée par <strong>%s</strong>',
                htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            default => htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8'),
        };
    }

    private function severityLabel(?int $priority): string
    {
        return self::SEVERITY_LABELS[$priority] ?? 'unknown';
    }

    private function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? $action;
    }

    private function actionIcon(string $action): string
    {
        return self::ACTION_ICONS[$action] ?? 'circle';
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../templates/layout.php';
    }
}

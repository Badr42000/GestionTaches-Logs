<?php

namespace Dashboard\Controller;

use Dashboard\Core\Database;
use PDO;

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
        'TASK_STATUS_CHANGED' => 'Changement statut',
        'TASK_LISTED' => 'Consultation liste',
        'TASK_VIEWED' => 'Consultation',
        'AUTH_LOGIN_SUCCESS' => 'Connexion réussie',
        'AUTH_LOGIN_FAILED' => 'Connexion échouée',
        'AUTH_REGISTER_SUCCESS' => 'Inscription réussie',
        'AUTH_REGISTER_FAILED' => 'Inscription échouée',
        'AUTH_LOGOUT' => 'Déconnexion',
        'SECURITY_ACCESS_DENIED' => 'Accès refusé',
        'SECURITY_RESOURCE_NOT_FOUND' => 'Ressource introuvable',
        'ERROR_DATABASE' => 'Erreur BDD',
        'ERROR_UNHANDLED' => 'Exception non gérée',
    ];

    private const ACTION_ICONS = [
        'TASK_CREATED' => 'circle-plus',
        'TASK_UPDATED' => 'pencil',
        'TASK_DELETED' => 'trash-2',
        'TASK_STATUS_CHANGED' => 'arrow-right',
        'TASK_LISTED' => 'list',
        'TASK_VIEWED' => 'eye',
        'AUTH_LOGIN_SUCCESS' => 'log-in',
        'AUTH_LOGIN_FAILED' => 'log-in',
        'AUTH_REGISTER_SUCCESS' => 'user-plus',
        'AUTH_REGISTER_FAILED' => 'user-plus',
        'AUTH_LOGOUT' => 'log-out',
        'SECURITY_ACCESS_DENIED' => 'shield-off',
        'SECURITY_RESOURCE_NOT_FOUND' => 'search',
        'ERROR_DATABASE' => 'database',
        'ERROR_UNHANDLED' => 'alert-triangle',
    ];

    private const ACTION_CATEGORIES = [
        'TASK_CREATED' => 'task',
        'TASK_UPDATED' => 'task',
        'TASK_DELETED' => 'task',
        'TASK_STATUS_CHANGED' => 'task',
        'TASK_LISTED' => 'task',
        'TASK_VIEWED' => 'task',
        'AUTH_LOGIN_SUCCESS' => 'auth',
        'AUTH_LOGIN_FAILED' => 'auth',
        'AUTH_REGISTER_SUCCESS' => 'auth',
        'AUTH_REGISTER_FAILED' => 'auth',
        'AUTH_LOGOUT' => 'auth',
        'SECURITY_ACCESS_DENIED' => 'security',
        'SECURITY_RESOURCE_NOT_FOUND' => 'security',
        'ERROR_DATABASE' => 'error',
        'ERROR_UNHANDLED' => 'error',
    ];

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function handleList(): void
    {
        $filter = $_GET['action'] ?? '';
        $category = $_GET['category'] ?? '';

        $allActions = array_keys(self::ACTION_LABELS);

        if ($filter && in_array($filter, $allActions, true)) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:'
                 AND Message LIKE ?
                 ORDER BY ReceivedAt DESC
                 LIMIT 200"
            );
            $stmt->execute(['%"action":"' . $filter . '"%']);
        } elseif ($category && in_array($category, ['task', 'auth', 'security', 'error'], true)) {
            $categoryActions = array_keys(
                array_filter(self::ACTION_CATEGORIES, fn($cat) => $cat === $category)
            );
            if (empty($categoryActions)) {
                $stmt = $this->pdo->query(
                    "SELECT * FROM SystemEvents
                     WHERE SysLogTag = 'tasklogger:'
                     ORDER BY ReceivedAt DESC
                     LIMIT 200"
                );
            } else {
                $placeholders = implode(' OR Message LIKE ', array_fill(0, count($categoryActions), '?'));
                $sql = "SELECT * FROM SystemEvents
                        WHERE SysLogTag = 'tasklogger:'
                        AND (Message LIKE $placeholders)
                        ORDER BY ReceivedAt DESC
                        LIMIT 200";
                $params = array_map(fn($a) => '%"action":"' . $a . '"%', $categoryActions);
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
            }
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
            'current_category' => $category,
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
        $total = (int)$stmt->fetchColumn();

        $allActions = array_keys(self::ACTION_LABELS);
        $actions = [];
        foreach ($allActions as $action) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:' AND Message LIKE ?"
            );
            $stmt->execute(['%"action":"' . $action . '"%']);
            $actions[$action] = (int)$stmt->fetchColumn();
        }

        $taskCount = 0;
        $authCount = 0;
        $securityCount = 0;
        $errorCount = 0;
        foreach ($actions as $action => $count) {
            $cat = self::ACTION_CATEGORIES[$action] ?? '';
            match ($cat) {
                'task' => $taskCount += $count,
                'auth' => $authCount += $count,
                'security' => $securityCount += $count,
                'error' => $errorCount += $count,
                default => null,
            };
        }

        return [
            'total' => $total,
            'actions' => $actions,
            'categories' => [
                'task' => $taskCount,
                'auth' => $authCount,
                'security' => $securityCount,
                'error' => $errorCount,
            ],
        ];
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../templates/layout.php';
    }
}

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
            'TASK_UPDATED' => sprintf(
                'Tâche <strong>%s</strong> modifiée par <strong>%s</strong>',
                htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            'TASK_STATUS_CHANGED' => sprintf(
                'Tâche <strong>%s</strong> : statut <strong>%s</strong> → <strong>%s</strong> par <strong>%s</strong>',
                htmlspecialchars($data['title'] ?? '#' . ($data['id'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['old_value'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['new_value'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            'TASK_DELETED' => sprintf(
                'Tâche <strong>%s</strong> supprimée par <strong>%s</strong>',
                htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            'TASK_LISTED' => sprintf(
                'Liste des tâches consultée par <strong>%s</strong> (%d tâche(s))',
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8'),
                (int)($data['count'] ?? 0)
            ),
            'TASK_VIEWED' => sprintf(
                'Tâche <strong>%s</strong> consultée par <strong>%s</strong>',
                htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_LOGIN_SUCCESS' => sprintf(
                'Connexion réussie de <strong>%s</strong> (IP : %s)',
                htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['ip'] ?? 'inconnue', ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_LOGIN_FAILED' => sprintf(
                'Échec de connexion pour <strong>%s</strong> (raison : %s, IP : %s)',
                htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['reason'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['ip'] ?? 'inconnue', ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_REGISTER_SUCCESS' => sprintf(
                'Inscription réussie de <strong>%s</strong> (IP : %s)',
                htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['ip'] ?? 'inconnue', ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_REGISTER_FAILED' => sprintf(
                'Échec d\'inscription pour <strong>%s</strong> (raison : %s, IP : %s)',
                htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['reason'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['ip'] ?? 'inconnue', ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_LOGOUT' => sprintf(
                'Déconnexion de <strong>%s</strong>',
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            'SECURITY_ACCESS_DENIED' => sprintf(
                'Accès refusé à <strong>%s</strong> (URI : %s, IP : %s)',
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['uri'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['ip'] ?? 'inconnue', ENT_QUOTES, 'UTF-8')
            ),
            'SECURITY_RESOURCE_NOT_FOUND' => sprintf(
                'Ressource introuvable : %s #%d par <strong>%s</strong>',
                htmlspecialchars($data['type'] ?? '', ENT_QUOTES, 'UTF-8'),
                (int)($data['id'] ?? 0),
                htmlspecialchars($data['username'] ?? 'inconnu', ENT_QUOTES, 'UTF-8')
            ),
            'ERROR_DATABASE' => sprintf(
                'Erreur base de données : %s',
                htmlspecialchars($data['message'] ?? $data['error'] ?? '', ENT_QUOTES, 'UTF-8')
            ),
            'ERROR_UNHANDLED' => sprintf(
                'Exception non gérée : %s dans %s:%d',
                htmlspecialchars($data['message'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(basename($data['file'] ?? ''), ENT_QUOTES, 'UTF-8'),
                (int)($data['line'] ?? 0)
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

    private function actionCategory(string $action): string
    {
        return self::ACTION_CATEGORIES[$action] ?? 'task';
    }
}

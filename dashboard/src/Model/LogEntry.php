<?php

namespace Dashboard\Model;

use PDO;
use PDOStatement;

class LogEntry extends AbstractModel
{
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
        'AUTH_LOGOUT' => 'Déconnexion',
        'SECURITY_RESOURCE_NOT_FOUND' => 'Ressource introuvable',
        'ERROR_UNHANDLED' => 'Exception non gérée',
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
        'AUTH_LOGOUT' => 'auth',
        'SECURITY_RESOURCE_NOT_FOUND' => 'security',
        'ERROR_UNHANDLED' => 'error',
    ];

    /** @return array<int, array<string, mixed>> */
    public function findAll(?string $filter = null, ?string $category = null, int $limit = 200): array
    {
        $allActions = array_keys(self::ACTION_LABELS);

        if ($filter && in_array($filter, $allActions, true)) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:'
                 AND Message LIKE ?
                 ORDER BY ReceivedAt DESC
                 LIMIT ?"
            );
            assert($stmt instanceof PDOStatement);
            $stmt->bindValue(1, '%"action":"' . $filter . '"%', PDO::PARAM_STR);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($category && in_array($category, ['task', 'auth', 'security', 'error'], true)) {
            $categoryActions = array_keys(
                array_filter(self::ACTION_CATEGORIES, fn($cat) => $cat === $category)
            );
            if (empty($categoryActions)) {
                $stmt = $this->pdo->prepare(
                    "SELECT * FROM SystemEvents
                     WHERE SysLogTag = 'tasklogger:'
                     ORDER BY ReceivedAt DESC
                     LIMIT ?"
                );
                assert($stmt instanceof PDOStatement);
                $stmt->bindValue(1, $limit, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $placeholders = implode(' OR Message LIKE ', array_fill(0, count($categoryActions), '?'));
                $sql = "SELECT * FROM SystemEvents
                        WHERE SysLogTag = 'tasklogger:'
                        AND (Message LIKE $placeholders)
                        ORDER BY ReceivedAt DESC
                        LIMIT ?";
                $params = array_map(fn($a) => '%"action":"' . $a . '"%', $categoryActions);
                $stmt = $this->pdo->prepare($sql);
                assert($stmt instanceof PDOStatement);
                foreach ($params as $i => $param) {
                    $stmt->bindValue($i + 1, $param, PDO::PARAM_STR);
                }
                $stmt->bindValue(count($params) + 1, $limit, PDO::PARAM_INT);
                $stmt->execute();
            }
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:'
                 ORDER BY ReceivedAt DESC
                 LIMIT ?"
            );
            assert($stmt instanceof PDOStatement);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed> */
    public function getStats(): array
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) AS total FROM SystemEvents WHERE SysLogTag = 'tasklogger:'"
        );
        assert($stmt instanceof PDOStatement);
        $total = (int)$stmt->fetchColumn();

        $allActions = array_keys(self::ACTION_LABELS);
        $actions = [];
        foreach ($allActions as $action) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM SystemEvents
                 WHERE SysLogTag = 'tasklogger:' AND Message LIKE ?"
            );
            assert($stmt instanceof PDOStatement);
            $stmt->execute(['%"action":"' . $action . '"%']);
            $actions[$action] = (int)$stmt->fetchColumn();
        }

        $taskCount = 0;
        $authCount = 0;
        $securityCount = 0;
        $errorCount = 0;
        foreach ($actions as $action => $count) {
            $cat = self::ACTION_CATEGORIES[$action];
            if ($cat === 'task') {
                $taskCount += $count;
            } elseif ($cat === 'auth') {
                $authCount += $count;
            } elseif ($cat === 'security') {
                $securityCount += $count;
            } elseif ($cat === 'error') {
                $errorCount += $count;
            }
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

    public static function severityLabel(?int $priority): string
    {
        return self::SEVERITY_LABELS[$priority] ?? 'unknown';
    }

    public static function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? $action;
    }

    public static function actionCategory(string $action): string
    {
        return self::ACTION_CATEGORIES[$action] ?? 'task';
    }

    /** @param array<string, string|int|float|bool|null> $data */
    public static function humanize(array $data): string
    {
        $action = $data['action'] ?? '';

        return match ($action) {
            'TASK_CREATED' => sprintf(
                'Tâche <strong>%s</strong> créée (priorité : %s) par <strong>%s</strong>',
                htmlspecialchars((string)($data['title'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['priority'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8')
            ),
            'TASK_UPDATED' => sprintf(
                'Tâche <strong>%s</strong> modifiée par <strong>%s</strong>',
                htmlspecialchars((string)($data['title'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8')
            ),
            'TASK_STATUS_CHANGED' => sprintf(
                'Tâche <strong>%s</strong> : statut <strong>%s</strong> → <strong>%s</strong> par <strong>%s</strong>',
                htmlspecialchars((string)($data['title'] ?? '#' . ($data['id'] ?? '')), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['old_value'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['new_value'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8')
            ),
            'TASK_DELETED' => sprintf(
                'Tâche <strong>%s</strong> supprimée par <strong>%s</strong>',
                htmlspecialchars((string)($data['title'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8')
            ),
            'TASK_LISTED' => sprintf(
                'Liste des tâches consultée par <strong>%s</strong> (%d tâche(s))',
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8'),
                intval($data['count'] ?? 0)
            ),
            'TASK_VIEWED' => sprintf(
                'Tâche <strong>%s</strong> consultée par <strong>%s</strong>',
                htmlspecialchars((string)($data['title'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_LOGIN_SUCCESS' => sprintf(
                'Connexion réussie de <strong>%s</strong>',
                htmlspecialchars((string)($data['username'] ?? ''), ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_LOGIN_FAILED' => sprintf(
                'Échec de connexion pour <strong>%s</strong> (raison : %s)',
                htmlspecialchars((string)($data['username'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)($data['reason'] ?? ''), ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_REGISTER_SUCCESS' => sprintf(
                'Inscription réussie de <strong>%s</strong>',
                htmlspecialchars((string)($data['username'] ?? ''), ENT_QUOTES, 'UTF-8')
            ),
            'AUTH_LOGOUT' => sprintf(
                'Déconnexion de <strong>%s</strong>',
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8')
            ),
            'SECURITY_RESOURCE_NOT_FOUND' => sprintf(
                'Ressource introuvable : %s #%d par <strong>%s</strong>',
                htmlspecialchars((string)($data['type'] ?? ''), ENT_QUOTES, 'UTF-8'),
                intval($data['id'] ?? 0),
                htmlspecialchars((string)($data['username'] ?? 'inconnu'), ENT_QUOTES, 'UTF-8')
            ),
            'ERROR_UNHANDLED' => sprintf(
                'Exception non gérée : %s dans %s:%d',
                htmlspecialchars((string)($data['message'] ?? ''), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(basename((string)($data['file'] ?? '')), ENT_QUOTES, 'UTF-8'),
                intval($data['line'] ?? 0)
            ),
            default => htmlspecialchars(json_encode($data) ?: '{}', ENT_QUOTES, 'UTF-8'),
        };
    }
}

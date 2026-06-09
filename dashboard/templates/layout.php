<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Logs des tâches</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0f1117;
            color: #e1e4e8;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 32px 24px; }
        header { margin-bottom: 32px; }
        header h1 { font-size: 28px; font-weight: 700; color: #f0f6fc; letter-spacing: -0.5px; }
        header p { color: #8b949e; margin-top: 4px; font-size: 14px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .stat-card {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 20px;
        }
        .stat-card .label { font-size: 13px; color: #8b949e; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card .value { font-size: 32px; font-weight: 700; color: #f0f6fc; margin-top: 4px; }
        .stat-card .value.created { color: #3fb950; }
        .stat-card .value.updated { color: #d29922; }
        .stat-card .value.deleted { color: #f85149; }
        .filters { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #30363d;
            border-radius: 8px;
            background: #161b22;
            color: #c9d1d9;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all .15s;
        }
        .filter-btn:hover { border-color: #58a6ff; color: #58a6ff; background: #1c2128; }
        .filter-btn.active { background: #1f6feb; color: #fff; border-color: #1f6feb; }
        .filter-btn.danger.active { background: #da3633; border-color: #da3633; }
        .logs { display: flex; flex-direction: column; gap: 8px; }
        .log-entry {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: border-color .15s;
        }
        .log-entry:hover { border-color: #58a6ff; }
        .log-icon {
            flex-shrink: 0;
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .log-icon.created { background: rgba(63,185,80,.15); color: #3fb950; }
        .log-icon.updated { background: rgba(210,153,34,.15); color: #d29922; }
        .log-icon.deleted { background: rgba(248,81,73,.15); color: #f85149; }
        .log-body { flex: 1; min-width: 0; }
        .log-header { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap; }
        .log-action {
            font-size: 12px; font-weight: 600;
            padding: 2px 10px; border-radius: 12px;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        .log-action.created { background: rgba(63,185,80,.15); color: #3fb950; }
        .log-action.updated { background: rgba(210,153,34,.15); color: #d29922; }
        .log-action.deleted { background: rgba(248,81,73,.15); color: #f85149; }
        .log-time { font-size: 12px; color: #6e7681; }
        .log-message { font-size: 14px; color: #e1e4e8; line-height: 1.5; }
        .log-message strong { color: #f0f6fc; }
        .log-meta { margin-top: 6px; display: flex; gap: 12px; font-size: 11px; color: #6e7681; }
        .log-severity { padding: 1px 8px; border-radius: 8px; font-weight: 500; }
        .log-severity.info { background: rgba(88,166,255,.15); color: #58a6ff; }
        .log-severity.err { background: rgba(248,81,73,.15); color: #f85149; }
        .log-severity.warning { background: rgba(210,153,34,.15); color: #d29922; }
        .log-severity.notice { background: rgba(139,148,158,.15); color: #8b949e; }
        .empty-state {
            text-align: center; padding: 60px 20px;
            background: #161b22; border: 1px solid #30363d;
            border-radius: 12px;
        }
        .empty-state p { color: #8b949e; font-size: 15px; }
        .footer { margin-top: 32px; text-align: center; font-size: 12px; color: #484f58; padding-bottom: 16px; }
        .refresh-bar {
            display: flex; justify-content: flex-end; margin-bottom: 16px; align-items: center; gap: 12px;
        }
        .refresh-bar .count { font-size: 13px; color: #8b949e; }
        .badge {
            display: inline-flex; align-items: center; justify-content: center;
            background: #21262d; color: #c9d1d9;
            font-size: 11px; font-weight: 600;
            padding: 1px 8px; border-radius: 10px; min-width: 20px;
        }
        @media (max-width: 640px) {
            .container { padding: 16px; }
            .stats { grid-template-columns: 1fr 1fr; }
            .log-entry { flex-direction: column; gap: 8px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard des logs</h1>
            <p>Suivi des actions réalisées sur la gestion de tâches</p>
        </header>

        <div class="stats">
            <div class="stat-card">
                <div class="label">Total</div>
                <div class="value"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Créations</div>
                <div class="value created"><?= $stats['actions']['TASK_CREATED'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Modifications</div>
                <div class="value updated"><?= $stats['actions']['TASK_UPDATED'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Suppressions</div>
                <div class="value deleted"><?= $stats['actions']['TASK_DELETED'] ?></div>
            </div>
        </div>

        <div class="filters">
            <a href="/" class="filter-btn <?= $current_filter === '' ? 'active' : '' ?>">Tous</a>
            <a href="/?action=TASK_CREATED" class="filter-btn <?= $current_filter === 'TASK_CREATED' ? 'active' : '' ?>">Créations</a>
            <a href="/?action=TASK_UPDATED" class="filter-btn <?= $current_filter === 'TASK_UPDATED' ? 'active' : '' ?>">Modifications</a>
            <a href="/?action=TASK_DELETED" class="filter-btn danger <?= $current_filter === 'TASK_DELETED' ? 'active' : '' ?>">Suppressions</a>
        </div>

        <div class="refresh-bar">
            <span class="count"><?= count($logs) ?> événement(s) affiché(s)</span>
        </div>

        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <p>Aucun log pour le moment.<br>Effectuez des actions sur la <a href="http://localhost:8081" style="color:#58a6ff">gestion de tâches</a>.</p>
            </div>
        <?php else: ?>
            <div class="logs">
                <?php foreach ($logs as $log):
                    $data = json_decode($log['Message'], true);
                    $action = $data['action'] ?? 'unknown';
                    $severity = $this->severityLabel((int)$log['Priority']);
                    $actionKey = strtolower(str_replace('TASK_', '', $action));
                ?>
                <div class="log-entry">
                    <div class="log-icon <?= $actionKey ?>">
                        <?= match ($action) {
                            'TASK_CREATED' => '+',
                            'TASK_UPDATED' => '~',
                            'TASK_DELETED' => '×',
                            default => '?',
                        } ?>
                    </div>
                    <div class="log-body">
                        <div class="log-header">
                            <span class="log-action <?= $actionKey ?>"><?= $this->actionLabel($action) ?></span>
                            <span class="log-time"><?= htmlspecialchars($log['ReceivedAt'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="log-message"><?= $this->humanize($data) ?></div>
                        <div class="log-meta">
                            <span class="log-severity <?= $severity ?>"><?= $severity ?></span>
                            <span>ID système : <?= (int)$log['ID'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="footer">
            Gestion de Tâches — Projet scolaire
        </div>
    </div>
</body>
</html>

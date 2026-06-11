        <div class="stats">
            <div class="stat-card">
                <div class="label">Total</div>
                <div class="value"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Tâches</div>
                <div class="value created"><?= $stats['categories']['task'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Authentification</div>
                <div class="value updated"><?= $stats['categories']['auth'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Sécurité</div>
                <div class="value deleted"><?= $stats['categories']['security'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Erreurs</div>
                <div class="value" style="color:#f85149"><?= $stats['categories']['error'] ?></div>
            </div>
        </div>

        <div class="filters">
            <a href="/" class="filter-btn <?= ($current_category === '' && $current_filter === '') ? 'active' : '' ?>">Tous</a>
            <a href="/?category=task" class="filter-btn <?= $current_category === 'task' ? 'active' : '' ?>">Tâches</a>
            <a href="/?category=auth" class="filter-btn <?= $current_category === 'auth' ? 'active' : '' ?>">Authentification</a>
            <a href="/?category=security" class="filter-btn danger <?= $current_category === 'security' ? 'active' : '' ?>">Sécurité</a>
            <a href="/?category=error" class="filter-btn <?= $current_category === 'error' ? 'active' : '' ?>">Erreurs</a>
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
                    $severity = \Dashboard\Model\LogEntry::severityLabel((int)$log['Priority']);
                    $category = \Dashboard\Model\LogEntry::actionCategory($action);

                    $icon = match (true) {
                        str_starts_with($action, 'TASK_') => match ($action) {
                            'TASK_CREATED' => '+',
                            'TASK_DELETED' => '×',
                            'TASK_STATUS_CHANGED' => '↻',
                            default => '◎',
                        },
                        str_starts_with($action, 'AUTH_') => match ($action) {
                            'AUTH_LOGIN_SUCCESS', 'AUTH_REGISTER_SUCCESS' => '✓',
                            'AUTH_LOGIN_FAILED', 'AUTH_REGISTER_FAILED' => '✗',
                            'AUTH_LOGOUT' => '←',
                            default => '?',
                        },
                        str_starts_with($action, 'SECURITY_') => '⚠',
                        str_starts_with($action, 'ERROR_') => '‼',
                        default => '?',
                    };
                ?>
                <div class="log-entry">
                    <div class="log-icon <?= $category ?>">
                        <?= $icon ?>
                    </div>
                    <div class="log-body">
                        <div class="log-header">
                            <span class="log-action <?= $category ?>"><?= \Dashboard\Model\LogEntry::actionLabel($action) ?></span>
                            <span class="log-time"><?= htmlspecialchars($log['ReceivedAt'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="log-message"><?= \Dashboard\Model\LogEntry::humanize($data) ?></div>
                        <div class="log-meta">
                            <span class="log-severity <?= $severity ?>"><?= $severity ?></span>
                            <span>ID système : <?= (int)$log['ID'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

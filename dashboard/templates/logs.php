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

        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <p>Aucune tâche pour le moment.</p>
            </div>
        <?php else: ?>
            <div style="background:#161b22;border:1px solid #30363d;border-radius:12px;overflow:hidden;">
                <table class="task-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Auteur</th>
                            <th>Créée le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td style="font-weight:500;color:#f0f6fc;"><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="priority-indicator">
                                    <span class="priority-dot <?= $task['priority'] ?>"></span>
                                    <span class="priority-<?= $task['priority'] ?>"><?= htmlspecialchars($task['priority'], ENT_QUOTES, 'UTF-8') ?></span>
                                </span>
                            </td>
                            <td><span class="status-badge <?= $task['status'] ?>"><?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td style="color:#8b949e;"><?= htmlspecialchars($task['created_by'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="color:#8b949e;font-size:13px;"><?= htmlspecialchars($task['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

<?php if (empty($tasks)): ?>
    <div class="card empty">
        <p>Aucune tâche pour le moment.</p>
        <a href="/create" class="btn btn-primary">Créer une tâche</a>
    </div>
<?php else: ?>
    <div class="card" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Créée le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-<?= $task['priority'] ?>"><?= htmlspecialchars($task['priority'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><span class="badge badge-<?= $task['status'] ?>"><?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= htmlspecialchars($task['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <div class="actions">
                            <form method="post" action="/status/<?= $task['id'] ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="todo" <?= $task['status'] === 'todo' ? 'selected' : '' ?>>Todo</option>
                                    <option value="in_progress" <?= $task['status'] === 'in_progress' ? 'selected' : '' ?>>In progress</option>
                                    <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Done</option>
                                </select>
                            </form>
                            <a href="/edit/<?= $task['id'] ?>" class="btn">Modifier</a>
                            <form method="post" action="/delete/<?= $task['id'] ?>" onsubmit="return confirm('Supprimer cette tâche ?')">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

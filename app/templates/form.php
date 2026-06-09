<div class="card">
    <form method="post">
        <label>Titre *</label>
        <input type="text" name="title" value="<?= htmlspecialchars($task['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($task['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

        <label>Priorité</label>
        <select name="priority">
            <option value="basse" <?= ($task['priority'] ?? '') === 'basse' ? 'selected' : '' ?>>Basse</option>
            <option value="moyenne" <?= ($task['priority'] ?? '') === 'moyenne' ? 'selected' : '' ?>>Moyenne</option>
            <option value="haute" <?= ($task['priority'] ?? '') === 'haute' ? 'selected' : '' ?>>Haute</option>
        </select>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/" class="btn">Annuler</a>
        </div>
    </form>
</div>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Tâches</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; max-width: 960px; margin: 0 auto; padding: 20px; background: #f8f9fa; color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        th, td { padding: 10px 14px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f1f3f5; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        .btn { display: inline-block; padding: 6px 14px; text-decoration: none; border: 1px solid #ccc; background: #fff; color: #333; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #228be6; color: #fff; border-color: #228be6; }
        .btn-primary:hover { background: #1c7ed6; }
        .btn-danger { background: #e03131; color: #fff; border-color: #e03131; }
        .btn-danger:hover { background: #c92a2a; }
        .btn-success { background: #2f9e44; color: #fff; border-color: #2f9e44; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 12px; font-weight: 500; }
        .badge-todo { background: #fff3bf; color: #856404; }
        .badge-in_progress { background: #a5d8ff; color: #004085; }
        .badge-done { background: #b2f2bb; color: #155724; }
        .badge-basse { background: #e9ecef; color: #495057; }
        .badge-moyenne { background: #ffec99; color: #856404; }
        .badge-haute { background: #ffc9c9; color: #c92a2a; }
        .error { color: #e03131; padding: 10px 14px; border: 1px solid #e03131; border-radius: 4px; margin-bottom: 15px; background: #fff5f5; }
        form label { display: block; margin-top: 12px; font-weight: 500; }
        form input, form textarea, form select { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        form textarea { height: 100px; resize: vertical; }
        .actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .actions select { padding: 4px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .header h1 { margin: 0; font-size: 24px; }
        .card { background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .empty { text-align: center; padding: 40px; color: #868e96; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestion de Tâches</h1>
        <div style="display:flex;align-items:center;gap:12px;">
            <?php if (!empty($_SESSION['user'])): ?>
                <span style="font-size:14px;color:#868e96;"><?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php if (!isset($hideCreate)): ?>
                    <a href="/create" class="btn btn-primary">+ Nouvelle tâche</a>
                <?php endif; ?>
                <a href="/logout" class="btn">Déconnexion</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php require __DIR__ . '/' . $template . '.php'; ?>
</body>
</html>

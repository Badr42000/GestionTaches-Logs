<div class="card" style="max-width:400px;margin:40px auto;">
    <h2 style="margin-bottom:20px;">Créer un compte</h2>
    <form method="post" action="/register">
        <label>Nom d'utilisateur</label>
        <input type="text" name="username" required autofocus>

        <label>Mot de passe</label>
        <input type="password" name="password" required>

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-primary" style="width:100%">S'inscrire</button>
        </div>
    </form>
    <p style="margin-top:16px;text-align:center;font-size:14px;color:#868e96;">
        Déjà un compte ? <a href="/login" style="color:#228be6;">Se connecter</a>
    </p>
</div>

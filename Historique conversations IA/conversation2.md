# Session du 09/06/2026 — Authentification + Dashboard tâches

## Synthèse

### 1. Authentification complète (login/register)
- Table `users` ajoutée à la base, avec un compte admin par défaut (admin/admin)
- Page de connexion (`/login`) avec session PHP
- Page d'inscription (`/register`) ouverte à tous avec validation (username unique, mot de passe ≥ 4 caractères)
- Connexion automatique après inscription
- Toutes les routes de l'appli 1 (TaskLogger) sont protégées : redirection vers `/login` si non connecté
- Le `username` est inclus dans chaque log syslog (format JSON)

### 2. Dashboard — vue tâches triées par priorité
- Nouvel onglet « Tâches » dans le Dashboard (à côté de « Logs »)
- Lecture directe de la table `tasks` avec tri : haute → moyenne → basse
- Indicateurs visuels : pastilles de couleur, statuts stylisés

### 3. Corrections / déploiement
- Les commits étaient présents en local mais pas push sur GitHub → `git push origin develop`
- Erreurs `Table 'tasklogger.users' doesn't exist` → nécessité de rebuild avec `docker compose down -v && docker compose up -d` pour que le nouvel `init.sql` s'exécute

---

## Questions / Réponses

**Q :** Penses-tu que c'est suffisant comme logs qu'on a ? Si ça ne l'est pas, on fait un système d'authentification, comme ça on saura qui a créé ou modifié ou supprimer une tâche.

**R :** Non, les logs ne sont pas suffisants — il manque l'identité de l'utilisateur qui effectue l'action. Actuellement on logue *quoi* mais pas *qui*. Un système d'authentification est pertinent pour ça. Cela apporte une table `users`, un login/logout par session PHP, et l'ajout du `username` dans chaque log syslog. Optionnellement, on peut aussi stocker le `created_by` dans la table `tasks`.

---

**Q :** Il faut que n'importe quel utilisateur puisse créer son compte et avoir accès à l'appli.

**R :** Ajout d'une page `/register` avec formulaire d'inscription. La validation vérifie que le username est unique et que le mot de passe fait au moins 4 caractères. Une fois inscrit, l'utilisateur est automatiquement connecté et redirigé vers la liste des tâches. Le lien « Pas encore de compte ? S'inscrire » est présent sur la page de login, et inversement.

# Historique de la conversation — Projet GestionDeTâches

## Contexte

Projet scolaire : développer deux applications PHP conteneurisées avec Docker.

- **Application 1** : GestionDeTâches (TaskLogger) — génère des logs métier
- **Application 2** : Dashboard de visualisation des logs

Contrainte principale : les logs doivent transiter par rsyslog avant d'être stockés.

---

## Architecture retenue

```
┌──────────┐
│   web    │ ───┐
│ PHP 8.2  │    │ UDP 514
│ TaskLogr │    │
└──────────┘    ▼
           ┌──────────┐     ┌─────────┐
           │ rsyslog  │ ──► │  mysql  │
           │ ommysql  │     │         │
           └──────────┘     │  tasks  │
                            │SystemEv.│
           ┌──────────┐     └─────────┘
           │dashboard │        ▲
           │ PHP 8.2  │────────┘
           │ visual.  │    PDO
           └──────────┘
```

### Services Docker

| Service | Rôle | Port |
|---|---|---|
| `web` | Application PHP de gestion des tâches, génère les logs | `8081` |
| `rsyslog` | Collecte les logs UDP et les insère en MySQL via `ommysql` | `514/udp` |
| `mysql` | Stocke les tâches (`tasks`) et les logs (`SystemEvents`) | `3306` |
| `dashboard` | Lit `SystemEvents` et affiche les logs dans une interface | `8080` |

### Choix techniques

| Élément | Décision | Justification |
|---|---|---|
| Serveur PHP | `php -S` (built-in) | Pas de Nginx, plus simple pour un projet scolaire |
| Stockage | MySQL (via PDO) | Compatible avec le module rsyslog `ommysql` |
| Logs | UDP 514 → rsyslog → MySQL | Architecture centralisée, le Dashboard lira la même base |
| Dépendances PHP | Aucune (PHP natif) | L'utilisateur ne connaît pas Composer, on évite les dépendances |
| Envoi UDP | `socket_sendto()` | Fonction native PHP, pas de bibliothèque externe |
| Autoloading | `spl_autoload_register` maison | ~8 lignes, zéro dépendance |
| Interface | Web (HTML/CSS natif) | Pas de framework JS, design dark mode fait main |

---

## Déroulement du projet

### Phase 1 : Infrastructure Docker

**Fichiers créés :**
- `docker-compose.yml` — orchestration des 3 services (web, rsyslog, mysql)
- `docker/web/Dockerfile` — PHP 8.2-cli avec extensions pdo_mysql + sockets
- `docker/rsyslog/Dockerfile` — Debian bookworm + rsyslog + rsyslog-mysql
- `docker/rsyslog/rsyslog.conf` — écoute UDP 514, envoi vers MySQL via ommysql
- `sql/init.sql` — tables `tasks` et `SystemEvents`
- `.gitignore`

**Erreur rencontrée :** Le package `rsyslog-mysql` de Debian déclenche `dbconfig-common` qui tente de configurer MySQL de manière interactive. MySQL n'étant pas dans le même conteneur, le build échoue.

**Résolution :** Pré-configuration de debconf pour désactiver l'installation automatique de la base :
```dockerfile
RUN echo 'rsyslog-mysql rsyslog-mysql/dbconfig-install boolean false' | debconf-set-selections && \
    apt-get install -y rsyslog rsyslog-mysql
```

**Commits :**
- `c50b97c` — Phase 1 : infrastructure Docker
- `ccac49a` — fix: rsyslog-mysql dbconfig non-interactive

---

### Phase 2 : Application PHP (TaskLogger)

**Fichiers créés :**
- `app/public/router.php` — routeur pour le serveur PHP built-in
- `app/public/index.php` — front controller avec routage des URL
- `app/src/autoload.php` — autoloader PSR-4 minimal
- `app/src/Database.php` — singleton PDO MySQL
- `app/src/Logger.php` — envoi UDP socket vers rsyslog (format RFC 3164)
- `app/src/TaskController.php` — CRUD + journalisation syslog
- `app/templates/layout.php` — template HTML avec CSS
- `app/templates/list.php` — liste des tâches avec actions
- `app/templates/form.php` — formulaire création/édition

**Fonctionnalités :**
- Créer une tâche (titre, description, priorité basse/moyenne/haute)
- Lister les tâches
- Changer le statut (todo → in_progress → done)
- Modifier / Supprimer
- Chaque action envoie un log UDP vers rsyslog

**Format des logs :** JSON dans le champ `Message` de `SystemEvents`
```json
{"action":"TASK_CREATED","id":1,"title":"Exemple","priority":"haute","status":"todo"}
```

**Commits :**
- `cbc9dfa` — Phase 2 : application PHP

---

### Phase 3 : Ports et IPv6

**Modification :** Déplacement du port web de 8080 → 8081 pour libérer le 8080 pour le futur Dashboard.

**Accès configuré :**
```
TaskLogger : http://[2a03:5840:111:1024:df:2cff:fe9a:36c]:8081
Dashboard  : http://[2a03:5840:111:1024:df:2cff:fe9a:36c]:8080
```

Docker bind automatiquement sur toutes les interfaces (IPv4 + IPv6) avec `ports: "8081:8080"`.

**Commits :**
- `6e63346` — fix: déplacer port web 8080 → 8081

---

### Phase 4 : Dashboard

**Nouveau service :** `dashboard` dans docker-compose, port 8080.

**Fichiers créés :**
- `docker/dashboard/Dockerfile` — PHP 8.2-cli avec pdo_mysql
- `dashboard/public/router.php`
- `dashboard/public/index.php`
- `dashboard/src/autoload.php`
- `dashboard/src/Database.php` — connexion MySQL (copie locale, pas de dépendance croisée)
- `dashboard/src/DashboardController.php` — lecture SystemEvents, parsing JSON, statistiques
- `dashboard/templates/layout.php` — design dark mode complet

**Fonctionnalités du Dashboard :**
- Statistiques en temps réel (total, créations, modifications, suppressions)
- Filtres par type d'action (boutons Créations / Modifications / Suppressions)
- Messages humanisés à partir du JSON brut
  - `TASK_CREATED` → Tâche **X** créée (priorité : haute)
  - `TASK_UPDATED` avec `field=status` → Tâche #1 : **todo** → **done**
  - `TASK_DELETED` → Tâche **X** supprimée
- Code couleur par action (vert = création, orange = modif, rouge = suppression)
- Code couleur par sévérité syslog (info, warning, err)
- Dark mode (palette GitHub Dark)

**Erreur rencontrée :** Le conteneur dashboard fraîchement créé ne voyait pas les fichiers du bind mount (`/var/www/html` vide alors que les fichiers existent sur l'hôte). Le problème venait d'un conteneur en cache.

**Résolution :** Recréation du conteneur :
```bash
docker compose stop dashboard
docker compose rm -f dashboard
docker compose up -d dashboard
```

---

## Branches Git

| Branche | Usage |
|---|---|
| `main` | Version stable livrée |
| `develop` | Développement actif |

Workflow : développements sur `develop` → merge dans `main` (pas de commit direct sur `main`).

---

## Structure finale du projet

```
GestionDeTâches/
├── docker-compose.yml
├── docker/
│   ├── web/Dockerfile
│   ├── dashboard/Dockerfile
│   └── rsyslog/
│       ├── Dockerfile
│       └── rsyslog.conf
├── sql/init.sql
├── app/
│   ├── public/
│   │   ├── router.php
│   │   └── index.php
│   ├── src/
│   │   ├── autoload.php
│   │   ├── Database.php
│   │   ├── Logger.php
│   │   └── TaskController.php
│   └── templates/
│       ├── layout.php
│       ├── list.php
│       └── form.php
├── dashboard/
│   ├── public/
│   │   ├── router.php
│   │   └── index.php
│   ├── src/
│   │   ├── autoload.php
│   │   ├── Database.php
│   │   └── DashboardController.php
│   └── templates/
│       └── layout.php
├── Historique conversations IA/
│   └── conversation.md
└── README.md
```

---

## Événements journalisés

| Action | Niveau syslog | Exemple de Message (JSON) |
|---|---|---|
| Création | info | `{"action":"TASK_CREATED","id":1,"title":"Test","priority":"haute","status":"todo"}` |
| Modification (statut) | info | `{"action":"TASK_UPDATED","id":1,"field":"status","old_value":"todo","new_value":"done"}` |
| Modification (général) | info | `{"action":"TASK_UPDATED","id":1,"title":"Test","priority":"moyenne"}` |
| Suppression | info | `{"action":"TASK_DELETED","id":1,"title":"Test"}` |

---

## Commandes utiles

```bash
# Démarrer la stack
docker compose up -d

# Arrêter la stack
docker compose down

# Supprimer les données (volume MySQL)
docker compose down -v

# Voir les logs d'un service
docker compose logs web
docker compose logs dashboard
docker compose logs rsyslog

# Inspection MySQL
docker compose exec mysql mysql -u tasklogger -ptasklogger tasklogger -e "SELECT * FROM SystemEvents"

# Recréer un conteneur (en cas de problème de cache)
docker compose stop <service>
docker compose rm -f <service>
docker compose up -d <service>
```

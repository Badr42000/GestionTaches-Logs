# Gestion de Tâches — TaskLogger

Projet scolaire — Application de gestion de tâches avec journalisation syslog centralisée.

## Architecture

4 conteneurs Docker :

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

- **web** : PHP 8.2 — interface de gestion des tâches (port 8081)
- **dashboard** : PHP 8.2 — visualisation des logs (port 8080)
- **rsyslog** : rsyslogd avec module `ommysql` — collecte les logs UDP et les insère en MySQL
- **mysql** : MySQL 8 — stocke les tâches (`tasks`) et les logs (`SystemEvents`)

## Prérequis

- Docker
- Docker Compose

## Lancement

```bash
docker compose up -d
```

Ouvrir [http://localhost:8081](http://localhost:8081)

Accès via IPv6 :
```
http://[2a03:5840:111:1024:df:2cff:fe9a:36c]:8081
```

Arrêt :
```bash
docker compose down
```

Pour supprimer les données (volume MySQL) :
```bash
docker compose down -v
```

## Fonctionnalités

- Créer une tâche (titre, description, priorité)
- Changer le statut (todo → in_progress → done)
- Modifier une tâche
- Supprimer une tâche
- Journalisation de chaque action dans MySQL via syslog

## Événements journalisés

| Action | Niveau | Message (JSON dans `Message`) |
|---|---|---|
| Création | INFO | `{"action":"TASK_CREATED","id":1,"title":"...",...}` |
| Modification | INFO | `{"action":"TASK_UPDATED","id":1,"field":"status",...}` |
| Suppression | INFO | `{"action":"TASK_DELETED","id":1,"title":"..."}` |

## Structure du projet

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
│   ├── public/ (index.php, router.php)
│   ├── src/ (Database, Logger, TaskController)
│   └── templates/ (layout, list, form)
├── dashboard/
│   ├── public/ (index.php, router.php)
│   ├── src/ (Database, DashboardController)
│   └── templates/ (layout)
└── README.md
```

## Ports

| Service | Port hôte | Port conteneur |
|---|---|---|
| TaskLogger | `8081` | 8080 |
| Dashboard | `8080` | 8080 |
| Rsyslog (UDP) | `514` | 514 |
| MySQL | `3306` | 3306 |

## Dashboard

Le Dashboard lit la table `SystemEvents` et affiche les logs avec :
- Statistiques en temps réel (total, créations, modifications, suppressions)
- Filtres par type d'action
- Messages humanisés à partir du JSON
- Code couleur par sévérité (info, warning, erreur)
- Design dark mode

Accessible sur [http://localhost:8080](http://localhost:8080) ou en IPv6 :
```
http://[2a03:5840:111:1024:df:2cff:fe9a:36c]:8080
```

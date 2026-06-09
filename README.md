# Gestion de Tâches — TaskLogger

Projet scolaire — Application de gestion de tâches avec journalisation syslog centralisée.

## Architecture

3 conteneurs Docker :

```
┌─────────┐   UDP 514   ┌──────────┐     ┌─────────┐
│   web   │ ──────────► │ rsyslog  │ ──► │  mysql  │
│  PHP 8  │             │ ommysql  │     │         │
│ CLI SVR │             │          │     │  tasks  │
└─────────┘             └──────────┘     │SystemEv.│
                                         └─────────┘
```

- **web** : PHP 8.2 CLI + serveur intégré (`php -S`) — interface de gestion des tâches
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
└── README.md
```

## Ports

| Service | Port hôte | Port conteneur |
|---|---|---|
| TaskLogger | `8081` | 8080 |
| Dashboard (à venir) | `8080` | — |
| Rsyslog (UDP) | `514` | 514 |
| MySQL | `3306` | 3306 |

## Dashboard (à venir)

Le Dashboard lira la table `SystemEvents` en base pour visualiser les logs.  
Il sera accessible sur [http://localhost:8080](http://localhost:8080).

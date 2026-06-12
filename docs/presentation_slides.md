---
marp: true
theme: uncover
class: invert
paginate: true
header: "TaskLogger — Supervision d'application"
footer: "Projet — Monitoring & Journalisation"
style: |
  section { font-family: 'Segoe UI', system-ui, sans-serif; }
  table { font-size: 0.7em; margin: 0 auto; }
  h1 { color: #58a6ff; }
  h2 { color: #58a6ff; border-bottom: 2px solid #30363d; padding-bottom: 0.3em; }
  .columns { display: flex; gap: 2em; justify-content: center; }
  .columns div { flex: 1; }
  .emoji-big { font-size: 3em; display: block; text-align: center; margin-bottom: 0.3em; }
---

# <!--fit--> TaskLogger

## Supervision & Journalisation Centralisée

<br>

Application de gestion de tâches avec monitoring temps réel

---

<!-- 1. Contexte -->
## Contexte (analyse de l'existant)

Mon entreprise possède déjà l'application **GestionDeTâches** (PHP)

**Problèmes :**
- ❌ Aucune supervision centralisée
- ❌ Aucune journalisation des actions
- ❌ Aucune détection des accès non autorisés
- ❌ Aucune visibilité en temps réel

---

<!-- 2. Expression du besoin -->
## Expression du besoin

<div class="columns">
<div>

**Pourquoi monitorer ?**

🛡️ **Sécurité** — Détection d'intrusions

📋 **Traçabilité** — Qui a fait quoi ?

🔧 **Débogage** — Erreurs identifiées

📊 **Métriques** — Statistiques d'usage
</div>
<div>

**Solution :**
- Système de logs centralisé
- Dashboard de supervision
- Journalisation de tous les événements

</div>
</div>

### Priorisation MoSCoW

| Priorité | Besoin |
|:--------:|--------|
| **Must** | Journalisation centralisée (rsyslog → MySQL) |
| **Must** | Journaliser tous les événements (auth, CRUD, sécurité, erreurs) |
| **Should** | Dashboard de supervision |
| **Could** | Filtres avancés et statistiques |
| **Won't** | Alerting temps réel (hors scope) |

---

<!-- 3. Objectifs SMART -->
## Objectifs du projet (SMART)

| # | Objectif | Livré |
|:-:|----------|:-----:|
| **O1** | Infrastructure Docker (4 conteneurs orchestrés) | ✅ |
| **O2** | Journalisation de 15 types d'événements | ✅ |
| **O3** | Dashboard dark mode avec filtres et stats | ✅ |
| **O4** | Traçabilité complète (action, user, IP, timestamp, détail métier) | ✅ |
| **O5** | Monitoring temps réel (latence < 10 ms) | ✅ |
| **O6** | Documentation complète (README, docs/, analyse ANSSI) | ✅ |

---

<!-- 4. Fonctions principales -->
## Fonctions principales

### GestionDeTâches — Port 8081

| Fonction | Journalisation |
|----------|:--------------:|
| Inscription | AUTH_REGISTER_SUCCESS / FAILED |
| Connexion / Déconnexion | AUTH_LOGIN_SUCCESS / FAILED / AUTH_LOGOUT |
| CRUD tâches | TASK_CREATED / UPDATED / DELETED |
| Changement statut | TASK_STATUS_CHANGED |
| Consultation tâche | TASK_VIEWED |
| Accès refusé / 404 | SECURITY_ACCESS_DENIED / RESOURCE_NOT_FOUND |

### Dashboard — Port 8080

| Fonction | Description |
|----------|-------------|
| Logs | Liste chronologique des événements syslog |
| Filtres | Par catégorie (Tâches, Auth, Sécurité, Erreurs) |
| Statistiques | Total d'événements par catégorie |
| Vue tâches | Liste des tâches avec priorité et statut |

---

<!-- 5. Critères de performance -->
## Critères de performance

| Critère | Exigence | Mesuré | Statut |
|---------|:--------:|:------:|:------:|
| Temps d'envoi d'un log | < 10 ms (UDP) | **1,06 ms** | ✅ |
| Temps d'affichage dashboard | < 2 s (200 logs) | **0,40 s** | ✅ |
| Volume supporté | > 10 000 évts/jour | **~86 400/j** | ✅ |
| Disponibilité services | 99 % (restart auto) | Docker restart | ✅ |
| Utilisateurs simultanés | Jusqu'à 50 | Non testé | ⚠️ |

> Protocole et résultats détaillés : `docs/mesures_performance.md`

---

<!-- 6. Contraintes techniques -->
## Contraintes techniques

| Contrainte | Détail |
|------------|--------|
| **PHP** | 8.2 CLI, PDO, sockets, ext-pdo_mysql |
| **Docker** | 4 conteneurs : web, dashboard, rsyslog, mysql |
| **rsyslog** | Debian Bookworm, module ommysql, UDP 514 |
| **MySQL** | MySQL 8, table SystemEvents |
| **Délai** | Moins d'1 semaine |
| **Réseau** | UDP (syslog), TCP/HTTP (applications) |

---

<!-- 7. Liste des livrables -->
## Liste des livrables

1. 🧩 **Code** — Application GestionDeTâches complète
2. 🧩 **Code** — Application Dashboard de supervision
3. 🐳 **Infrastructure** — docker-compose.yml, Dockerfiles, rsyslog.conf
4. 🗄️ **Base de données** — init.sql (tables, users)
5. 📖 **Documentation** — README.md, docs/ (analyse ANSSI, tests, performance, risques, suivi)
6. 📑 **Présentation** — docs/projet_presentation.md + presentation_slides.md
7. 🌐 **Dépôt Git** — Historique des commits sur GitHub

---

<!-- 8. Tâches par livrable / répartition -->
## Tâches par livrable

| Livrable | Tâches | Effort |
|----------|--------|:------:|
| **Infra Docker** | docker-compose, Dockerfiles, rsyslog.conf, MySQL init | 6,5 h |
| **GestionDeTâches** | Autoloader MVC, Database, SyslogLogger, AuthController, TaskController, templates | 8,5 h |
| **Dashboard** | Structure, DashboardController, templates dark mode | 6 h |
| **Réseau** | Ports, IPv6 | 1 h |
| **Documentation** | README, présentation, analyse ANSSI, échanges IA | 6 h |
| **Qualité** | Refacto MVC, mutualisation, PHPUnit, PHPStan, tests validation | 6,5 h |
| **Finalisation** | Registre risques, suivi projet | 1 h |

**Total : ~33 h estimées, ~26 h réalisées** sur 3 jours (09-11 juin 2026)

### Répartition
Projet **mono-auteur** — 100 % des tâches réalisées par le même développeur.

---

<!-- 9. Matériels et logiciels -->
## Matériels et logiciels

<div class="columns">
<div>

- 🐧 **OS** : Linux (Debian/Ubuntu)
- 🐘 **Langage** : PHP 8.2
- 🗄️ **BDD** : MySQL 8
- 🐳 **Conteneurs** : Docker + Compose
- 📡 **Logs** : rsyslog (ommysql + imudp)
</div>
<div>

- 🌿 **Versioning** : Git + GitHub
- ✏️ **Éditeur** : VS Code / PHPStorm
- 📦 **Serveur** : PHP CLI intégré (`php -S`)
- 🔗 **Protocole** : UDP 514, HTTP 8080/8081
- 🧪 **Test** : Tests manuels navigateur
</div>
</div>

---

<!-- 10. UML — Diagramme de cas d'utilisation -->
## UML — Diagramme de cas d'utilisation

```
┌──────────────────────────────────────┐
│           Utilisateur                │
└──────────┬───────────────────────────┘
           │
     ┌─────┴─────┐
     │ S'inscrire │      Superviseur
     │ Connexion  │     ┌──────────┐
     │ Déconnexion│     │ Consulter│
     │ CRUD Tâches│     │ les logs │
     │ Statut     │     │ Filtrer  │
     │ Consultation│    │ Stats    │
     └───────────┘      └──────────┘
```

### GestionDeTâches — 9 cas d'utilisation

| N° | Cas | Acteur |
|:--:|-----|--------|
| UC1-UC3 | S'inscrire, se connecter, se déconnecter | Utilisateur |
| UC4-UC7 | CRUD tâches + changement statut | Utilisateur |
| UC8-UC9 | Lister, consulter une tâche | Utilisateur |

*UC5/UC6/UC7 incluent UC9 (consultation préalable)*

### Dashboard — 4 cas d'utilisation

| N° | Cas | Acteur |
|:--:|-----|--------|
| UC10 | Consulter les logs | Superviseur |
| UC11 | Filtrer les logs | Superviseur |
| UC12 | Voir les statistiques | Superviseur |
| UC13 | Consulter les tâches | Superviseur |

> Diagramme UML complet : `docs/diagrams/use_case.puml`

---

<!-- 11. UML — Diagramme de déploiement -->
## UML — Diagramme de déploiement

```
┌───────────────────────────────────────────┐
│              Docker Host                   │
│                                            │
│  ┌────────────┐     ┌────────────┐         │
│  │  web:8081  │     │dashboard:8080│       │
│  │GestionTâches│    │ Supervision │         │
│  └─────┬──────┘     └──────┬─────┘         │
│        │ UDP 514           │ PDO           │
│        ▼                   ▼               │
│  ┌────────────┐     ┌────────────┐         │
│  │  rsyslog   │────►│ mysql:3306 │         │
│  │ (ommysql)  │     │ SystemEvents│        │
│  └────────────┘     └────────────┘         │
└───────────────────────────────────────────┘
```

### Détail des nœuds

| Nœud | Technologie | Port | Rôle |
|------|-------------|:----:|------|
| **web** | PHP 8.2 CLI | 8081 | Application GestionDeTâches |
| **rsyslog** | rsyslogd + ommysql | 514/UDP | Collecteur de logs |
| **mysql** | MySQL 8 | 3306 | Stockage données + logs |
| **dashboard** | PHP 8.2 CLI | 8080 | Interface de supervision |

> Diagramme UML : `docs/diagrams/deployment.puml`

---

<!-- 12. Schéma réseau / synoptique -->
## Schéma synoptique

```
Utilisateur ──HTTP 8081──► GestionDeTâches
                                   │
                          UDP 514  │ syslog
                                   ▼
                              rsyslog
                                   │
                          ommysql  │
                                   ▼
                              MySQL 8
                                   │
                          PDO      │
                                   ▼
Superviseur ──HTTP 8080──► Dashboard
```

### Flux réseau

| Étape | De | Vers | Protocole |
|:-----:|----|------|:---------:|
| 1 | Utilisateur | GestionDeTâches | HTTP 8081 |
| 2 | GestionDeTâches | rsyslog | UDP 514 |
| 3 | rsyslog | MySQL | TCP 3306 |
| 4 | Dashboard | MySQL | TCP 3306 (PDO) |
| 5 | Superviseur | Dashboard | HTTP 8080 |

**Réseau :** bridge Docker dédié (`gestiondetaches_default`), pas d'exposition MySQL à l'extérieur.

---

<!-- 13. Sitemap -->
## Sitemap

### GestionDeTâches (:8081)

| Route | Méthode | Accès |
|-------|:-------:|:-----:|
| `/login` | GET / POST | 🔓 Public |
| `/register` | GET / POST | 🔓 Public |
| `/logout` | GET | 🔒 Auth |
| `/` | GET | 🔒 Auth |
| `/create` | GET / POST | 🔒 Auth |
| `/edit/{id}` | GET / POST | 🔒 Auth |
| `/delete/{id}` | POST | 🔒 Auth |
| `/status/{id}` | POST | 🔒 Auth |

### Dashboard (:8080) — 🔓 Accès libre

| Route | Page |
|-------|------|
| `/` | Logs + stats + filtres |
| `/?category={cat}` | Logs filtrés |
| `/tasks` | Liste des tâches |

---

<!-- 14. Mockups -->
## Mockups

### Dashboard — Logs

```
┌──────────────────────────────────────┐
│  Dashboard des logs                  │
│                                      │
│ [Logs] [Tâches]                      │
│ ┌─────┬─────┬──────┬──────┬──────┐   │
│ │TOTAL│Tâche│ Auth │Sécu.│Erreur│   │
│ │ 42  │ 24  │  12  │  4  │  2   │   │
│ └─────┴─────┴──────┴──────┴──────┘   │
│ [Tous] [Tâches] [Auth] [Sécu] [Err]  │
│ ┌──────────────────────────────┐     │
│ │ [+] Création    12:30        │     │
│ │ Tâche "Rapport" créée        │     │
│ │ par admin • info • ID:42    │     │
│ ├──────────────────────────────┤     │
│ │ [✓] Connexion   12:28       │     │
│ │ Connexion réussie de admin  │     │
│ └──────────────────────────────┘     │
└──────────────────────────────────────┘
```

### GestionDeTâches — Connexion & Liste

```
┌──────────────────────┐   ┌──────────────────────┐
│    Connexion         │   │ Liste des tâches     │
│                      │   │ [Nouvelle tâche]     │
│ Identifiant [    ]   │   │ Titre    │ Priorité  │
│ Mot passe  [    ]   │   │ Rapport  │ [haute]   │
│ [Se connecter]      │   │ Réunion  │ [moyenne] │
│ S'inscrire          │   │ Rangement│ [basse]   │
└──────────────────────┘   └──────────────────────┘
```

> 6 mockups détaillés : `docs/mockups/mockups.md`

---

<!-- Recette — Tests de validation -->
## Tests de validation

| N° | Test | Cas d'utilisation | Résultat |
|:--:|------|:-----------------:|:--------:|
| 1 | Connexion réussie | UC2 | ✅ |
| 2 | Connexion échouée | UC2 | ✅ |
| 3 | Inscription réussie | UC1 | ✅ |
| 4 | Inscription doublon | UC1 | ✅ |
| 5 | Création de tâche | UC4 | ✅ |
| 6 | Changement statut | UC7 | ✅ |
| 7 | Accès refusé sans session | UC2 (alt) | ✅ |
| 8 | Ressource introuvable | — | ✅ |
| 9 | Dashboard : logs visibles | UC10 | ✅ |
| 10 | Filtre par catégorie | UC11 | ✅ |

> Résultats détaillés avec preuves : `docs/validations/recette_resultats.md`

---

<!-- Démonstration -->
## Démonstration

### Procédure

1. 🐳 `docker compose up -d`
2. 🌐 **http://localhost:8081** — Actions utilisateur
3. 📊 **http://localhost:8080** — Dashboard supervision

### Vérifications
- ✅ Logs visibles quasi instantanément (UDP + ommysql)
- ✅ Chaque action utilisateur génère exactement 1 log
- ✅ Filtres par catégorie fonctionnels
- ✅ Statistiques cohérentes
- ✅ Les échecs sont tracés avec la raison
- ✅ Adresses IP enregistrées (auth, sécurité)

---

<!-- Bilan évaluation -->
## Bilan — Évaluation CPI-2026

| Partie | Note | Ratio |
|:------:|:----:|:-----:|
| **A** — Gestion de projet (/6) | 4,50 / 6 | 75 % |
| **B** — Cahier des charges (/5) | 4,26 / 5 | 85 % |
| **C** — Développement (/4) | 3,00 / 4 | 75 % |
| **D** — Soutenance (/5) | — | À venir |
| **Total** | **15,5 / 20** | |

> Détail complet : `docs/evaluation_prof.md`

---

# <!--fit--> Merci

<br>

**TaskLogger** — Supervision & Journalisation

Dépôt : [github.com/Badr42000/GestionTaches-Logs](https://github.com/Badr42000/GestionTaches-Logs)

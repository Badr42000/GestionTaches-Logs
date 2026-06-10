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

## Contexte

Mon entreprise possède déjà l'application **GestionDeTâches** (PHP)

**Problèmes :**
- ❌ Aucune supervision centralisée
- ❌ Aucune journalisation des actions
- ❌ Aucune détection des accès non autorisés
- ❌ Aucune visibilité en temps réel

---

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

---

## Objectifs du projet

1. 📡 Infrastructure de **journalisation centralisée** (rsyslog)
2. 📝 **Tracer** authentification, tâches, sécurité, erreurs
3. 📊 **Dashboard** de supervision avec filtres et stats
4. 🔍 **Traçabilité complète** des actions utilisateur
5. ⚡ **Monitoring temps réel** de l'activité

---

## Architecture applicative

### Deux applications complémentaires

<div class="columns">
<div>

**GestionDeTâches** — Port 8081
- Inscription / Connexion
- CRUD de tâches
- Changement de statut
- Génération des logs

</div>
<div>

**Dashboard** — Port 8080
- Affichage des logs
- Filtres par catégorie
- Statistiques
- Vue tâches

</div>
</div>

---

## Événements journalisés

| Catégorie | Événements |
|-----------|------------|
| 🔐 **Authentification** | Connexion (succès/échec), Inscription (succès/échec), Déconnexion |
| 📋 **Tâches** | Création, Modification, Suppression, Changement statut, Consultation |
| 🛡️ **Sécurité** | Accès refusé, Ressource introuvable |
| ⚠️ **Erreurs** | Erreur BDD, Exception non gérée |

---

## Critères de performance

| Critère | Objectif |
|---------|----------|
| Envoi d'un log | < 10 ms (UDP) |
| Affichage dashboard | < 2 secondes |
| Volume supporté | > 10 000 évts/jour |
| Utilisateurs simultanés | Jusqu'à 50 |
| Disponibilité | 99 % |

---

## Contraintes techniques

| Technologie | Détail |
|-------------|--------|
| **PHP** | 8.2 CLI, PDO, sockets |
| **Docker** | 4 conteneurs |
| **rsyslog** | ommysql, UDP 514 |
| **MySQL** | 8 — table SystemEvents |
| **Délai** | Moins d'1 semaine |

---

## Liste des livrables

1. 🧩 **Code** — Application GestionDeTâches
2. 🧩 **Code** — Dashboard de supervision
3. 🐳 **Infra Docker** — docker-compose + Dockerfiles
4. 🗄️ **Base SQL** — init.sql
5. 📖 **Documentation** — README.md
6. 📑 **Présentation** — docs/projet_presentation.md
7. 🌐 **Dépôt Git** — GitHub

---

## Tâches par livrable

| Livrable | Tâches |
|----------|--------|
| **GestionDeTâches** | Logger, AuthController, TaskController, Database |
| **Dashboard** | DashboardController, Templates, Filtres, Stats |
| **Infrastructure** | Docker, docker-compose, rsyslog.conf, MySQL |
| **Documentation** | README, présentation, tests |

---

## Matériels & logiciels

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
- 📦 **Serveur** : PHP CLI intégré
- 🔗 **Protocole** : UDP 514, HTTP 8080/8081
</div>
</div>

---

## Diagramme de cas d'utilisation

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

---

## Use cases — GestionDeTâches

| N° | Cas | Acteur |
|:--:|-----|--------|
| UC1 | S'inscrire | Utilisateur |
| UC2 | Se connecter | Utilisateur |
| UC3 | Se déconnecter | Utilisateur |
| UC4 | Créer une tâche | Utilisateur |
| UC5 | Modifier une tâche | Utilisateur |
| UC6 | Supprimer une tâche | Utilisateur |
| UC7 | Changer le statut | Utilisateur |
| UC8 | Lister les tâches | Utilisateur |
| UC9 | Consulter une tâche | Utilisateur |

---

## Use cases — Dashboard

| N° | Cas | Acteur |
|:--:|-----|--------|
| UC10 | Consulter les logs | Superviseur |
| UC11 | Filtrer les logs | Superviseur |
| UC12 | Voir les statistiques | Superviseur |
| UC13 | Consulter les tâches | Superviseur |

---

## Diagramme de déploiement

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

---

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

**Échanges :**
1. Action utilisateur → GestionDeTâches
2. Envoi syslog UDP → rsyslog
3. Insertion MySQL via ommysql
4. Dashboard interroge MySQL
5. Affichage des logs

---

## Sitemap — GestionDeTâches

| Route | Page |
|-------|------|
| `/login` | Connexion |
| `/register` | Inscription |
| `/logout` | Déconnexion |
| `/` | Liste des tâches |
| `/create` | Création de tâche |
| `/edit/{id}` | Modification |
| `/delete/{id}` | Suppression |
| `/status/{id}` | Changement statut |

---

## Sitemap — Dashboard

| Route | Page |
|-------|------|
| `/` | Logs + filtres + stats |
| `/?category=task` | Logs tâches |
| `/?category=auth` | Logs authentification |
| `/?category=security` | Logs sécurité |
| `/?category=error` | Logs erreurs |
| `/tasks` | Vue tâches |

---

## Mockup — Connexion

```
┌────────────────────────────────┐
│        Gestion de Tâches       │
│                                │
│   ┌────────────────────────┐   │
│   │       Connexion        │   │
│   │                        │   │
│   │  Identifiant [    ]    │   │
│   │  Mot de passe [    ]   │   │
│   │                        │   │
│   │  [ Se connecter ]      │   │
│   │                        │   │
│   │  Pas de compte ?       │   │
│   │  S'inscrire            │   │
│   └────────────────────────┘   │
└────────────────────────────────┘
```

---

## Mockup — Liste des tâches

```
┌────────────────────────────────┐
│  Gestion de Tâches    admin  🚪│
│                                │
│  [📝 Nouvelle tâche]           │
│                                │
│  Titre     │ Priorité │ Statut │
│ ───────────┼──────────┼─────── │
│  Rapport   │ 🔴 haute │ ◌ todo │
│  Réunion   │ 🟡 moy.  │ ▶ en c.│
│  Rangement │ ⚪ basse  │ ✅ done│
└────────────────────────────────┘
```

---

## Mockup — Dashboard logs

```
┌──────────────────────────────────────┐
│  Dashboard des logs                  │
│                                      │
│ [Logs] [Tâches]                      │
│                                      │
│ ┌─────┬─────┬──────┬──────┬──────┐   │
│ │TOTAL│Tâche│ Auth │Sécu.│Erreur│   │
│ │ 42  │ 24  │  12  │  4  │  2   │   │
│ └─────┴─────┴──────┴──────┴──────┘   │
│                                      │
│ [Tous] [Tâches] [Auth] [Sécu] [Err]  │
│                                      │
│ ┌──────────────────────────────┐     │
│ │ [+] Création    12:30        │     │
│ │ Tâche "Rapport" créée        │     │
│ │ par admin • info • ID:42    │     │
│ ├──────────────────────────────┤     │
│ │ [✓] Connexion   12:28       │     │
│ │ Connexion réussie de admin  │     │
│ │ IP: 172.19.0.1 • info       │     │
│ └──────────────────────────────┘     │
└──────────────────────────────────────┘
```

---

## Plan de recette

| N° | Test | Résultat attendu |
|:--:|------|------------------|
| 1 | Connexion réussie | Log AUTH_LOGIN_SUCCESS |
| 2 | Connexion échouée | Log AUTH_LOGIN_FAILED |
| 3 | Inscription réussie | Log AUTH_REGISTER_SUCCESS |
| 4 | Inscription doublon | Log AUTH_REGISTER_FAILED |
| 5 | Création de tâche | Log TASK_CREATED |
| 6 | Changement statut | Log TASK_STATUS_CHANGED |
| 7 | Accès refusé | Log SECURITY_ACCESS_DENIED |
| 8 | Ressource introuvable | Log SECURITY_RESOURCE_NOT_FOUND |
| 9 | Dashboard logs | Logs visibles toutes catégories |
| 10 | Filtre catégorie | Filtre fonctionnel |

---

## Démonstration

### Procédure

1. 🐳 `docker compose up -d`
2. 🌐 **http://localhost:8081** — Actions utilisateur
3. 📊 **http://localhost:8080** — Dashboard logs

### Vérifications
- ✅ Logs visibles quasi instantanément
- ✅ Filtres par catégorie fonctionnels
- ✅ Statistiques cohérentes
- ✅ Chaque action = 1 log

---

# <!--fit--> Merci

<br>

**TaskLogger** — Supervision & Journalisation

Dépôt : [github.com/Badr42000/GestionTaches-Logs](https://github.com/Badr42000/GestionTaches-Logs)

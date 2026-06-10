# Projet TaskLogger — Présentation

---

## Contexte (analyse de l'existant)

Une entreprise fictive possède déjà l'application **GestionDeTâches**, une solution PHP de gestion de tâches permettant aux utilisateurs de créer, modifier, supprimer et suivre l'état d'avancement de leurs tâches.

**État des lieux :**
- L'application répond au besoin métier (gestion des tâches quotidiennes)
- Aucune solution de supervision centralisée n'existe
- Les événements utilisateur (connexions, actions, erreurs) ne sont pas tracés
- Impossible de détecter les tentatives d'accès non autorisées
- Pas de tableau de bord pour visualiser l'activité

**Problèmes identifiés :**
- Aucune journalisation des authentifications (réussies ou échouées)
- Aucune journalisation des accès refusés
- Aucune journalisation des erreurs applicatives ou base de données
- Aucune visibilité sur l'activité utilisateur en temps réel

---

## Expression du besoin

**Pourquoi le monitoring est-il nécessaire ?**

1. **Sécurité** : Détecter les tentatives de connexion frauduleuses, les accès non autorisés, les comportements anormaux.
2. **Traçabilité** : Savoir qui a fait quoi et quand (contexte de responsabilité).
3. **Débogage** : Identifier rapidement les erreurs applicatives et les pannes base de données.
4. **Métriques** : Disposer de statistiques d'utilisation (nombre de tâches créées, modifiées, supprimées).
5. **Conformité** : Respecter les bonnes pratiques de journalisation (recommandations ANSSI).

**Besoin exprimé :**
- Mettre en place un système de journalisation centralisé
- Créer un dashboard de supervision
- Journaliser TOUS les événements pertinents (auth, CRUD, sécurité, erreurs)

---

## Objectifs du projet

1. **Mettre en place une infrastructure de journalisation centralisée** via syslog (rsyslog)
2. **Journaliser un maximum d'événements** : authentification, gestion des tâches, sécurité, erreurs
3. **Développer un dashboard de supervision** affichant les logs avec filtres et statistiques
4. **Assurer la traçabilité complète** des actions utilisateur
5. **Permettre un monitoring en temps réel** de l'activité applicative
6. **Documenter l'architecture et les choix techniques** pour maintenabilité

---

## Fonctions principales

### Application 1 : GestionDeTâches (port 8081)

Application PHP de gestion de tâches avec les fonctionnalités suivantes :

| Fonction | Description | Journalisation |
|----------|-------------|----------------|
| Inscription | Création d'un compte utilisateur | AUTH_REGISTER_SUCCESS / AUTH_REGISTER_FAILED |
| Connexion | Authentification par identifiant/mot de passe | AUTH_LOGIN_SUCCESS / AUTH_LOGIN_FAILED |
| Déconnexion | Fin de session | AUTH_LOGOUT |
| Liste des tâches | Visualisation de toutes les tâches | TASK_LISTED |
| Création de tâche | Ajout d'une nouvelle tâche | TASK_CREATED |
| Modification de tâche | Édition du titre, description, priorité | TASK_UPDATED |
| Suppression de tâche | Suppression d'une tâche existante | TASK_DELETED |
| Changement de statut | todo → in_progress → done | TASK_STATUS_CHANGED |
| Consultation détail | Visualisation d'une tâche spécifique | TASK_VIEWED |

### Application 2 : Dashboard de supervision (port 8080)

Interface de monitoring avec :

| Fonction | Description |
|----------|-------------|
| Affichage des logs | Liste chronologique des événements syslog |
| Filtrage par catégorie | Tâches, Authentification, Sécurité, Erreurs |
| Filtrage par action | Par type d'événement spécifique |
| Statistiques | Total des événements par catégorie |
| Vue tâches | Liste des tâches avec priorité et statut |
| Indicateurs visuels | Couleurs et icônes par sévérité et type |

---

## Critères de performance

| Critère | Exigence |
|---------|----------|
| Temps d'envoi d'un log | < 10 ms (UDP local) |
| Temps d'affichage du dashboard | < 2 secondes pour 200 logs |
| Volume de logs supporté | > 10 000 événements par jour |
| Disponibilité des services | 99 % (redémarrage Docker automatique) |
| Temps d'arrêt maximum | < 5 minutes (conteneurs stateless) |
| Nombre d'utilisateurs simultanés | Jusqu'à 50 sur GestionDeTâches |
| Rafraîchissement dashboard | Manuel (rechargement navigateur) |

---

## Contraintes techniques

| Contrainte | Détail |
|------------|--------|
| **PHP** | PHP 8.2 (CLI), PDO, sockets, ext-pdo_mysql |
| **Docker** | 4 conteneurs : web, dashboard, rsyslog, mysql |
| **rsyslog** | Version Debian Bookworm, module ommysql, UDP 514 |
| **GitHub** | Dépôt : Badr42000/GestionTaches-Logs |
| **Linux** | Déploiement sur environnement Linux (Debian/Ubuntu) |
| **Délai de réalisation** | Projet scolaire — 2 semaines |
| **SGBD** | MySQL 8, table SystemEvents pour les logs |
| **Réseau** | UDP pour syslog, TCP/HTTP pour les applications web |
| **Sécurité** | Aucune authentification sur le dashboard (accès libre en interne) |

---

## Liste des livrables

1. **Code source** : application GestionDeTâches complète
2. **Code source** : application Dashboard de supervision
3. **Infrastructure Docker** : docker-compose.yml, Dockerfiles, rsyslog.conf
4. **Base de données** : script SQL d'initialisation (init.sql)
5. **Documentation** : README.md
6. **Document de présentation** : docs/projet_presentation.md
7. **Dépôt Git** : historique des commits sur GitHub

---

## Répartition des tâches (exemple pour 4 étudiants)

| Étudiant | Rôle | Tâches |
|----------|------|--------|
| **Étudiant A** | Développeur backend | Logger.php, AuthController, TaskController, Database |
| **Étudiant B** | Développeur dashboard | DashboardController, templates, filtres, statistiques |
| **Étudiant C** | DevOps / Infrastructure | Docker, docker-compose, rsyslog, MySQL, déploiement |
| **Étudiant D** | Documentation / Tests | Tests, documentation, ANSSI, plan de recette |

---

## Matériels et logiciels utilisés

| Élément | Technologie |
|---------|-------------|
| Système d'exploitation | Linux (Debian/Ubuntu) |
| Langage | PHP 8.2 |
| Base de données | MySQL 8 |
| Conteneurisation | Docker + Docker Compose |
| Serveur HTTP intégré | PHP CLI (php -S) |
| Journalisation | rsyslog (ommysql, imudp) |
| Versioning | Git + GitHub |
| Éditeur | VS Code, PHPStorm |
| Test | Tests manuels via navigateur |
| Protocole réseau | UDP 514 (syslog), HTTP 8080/8081 |

---

## UML — Diagramme de cas d'utilisation

### Acteurs

| Acteur | Description |
|--------|-------------|
| **Utilisateur** | Personne utilisant l'application GestionDeTâches |
| **Admin / Superviseur** | Personne consultant le Dashboard de supervision |

### Cas d'utilisation — GestionDeTâches

```
┌──────────────────────────────────────┐
│           Utilisateur                │
└──────────┬───────────────────────────┘
           │
    ┌──────┴──────┐
    │  S'inscrire  │
    │  Se connecter│
    │  Se déconnecter│
    │  Créer une tâche│
    │  Modifier une tâche│
    │  Supprimer une tâche│
    │  Changer le statut│
    │  Lister les tâches│
    │  Consulter une tâche│
    └──────────────┘
```

| N° | Cas d'utilisation | Acteur | Description |
|----|-------------------|--------|-------------|
| UC1 | S'inscrire | Utilisateur | Créer un compte avec identifiant et mot de passe |
| UC2 | Se connecter | Utilisateur | S'authentifier avec ses identifiants |
| UC3 | Se déconnecter | Utilisateur | Mettre fin à sa session |
| UC4 | Créer une tâche | Utilisateur | Ajouter une tâche (titre, description, priorité) |
| UC5 | Modifier une tâche | Utilisateur | Éditer une tâche existante |
| UC6 | Supprimer une tâche | Utilisateur | Supprimer une tâche |
| UC7 | Changer le statut | Utilisateur | Modifier le statut (todo/in_progress/done) |
| UC8 | Lister les tâches | Utilisateur | Voir toutes ses tâches |
| UC9 | Consulter une tâche | Utilisateur | Voir le détail d'une tâche |

### Cas d'utilisation — Dashboard

| N° | Cas d'utilisation | Acteur | Description |
|----|-------------------|--------|-------------|
| UC10 | Consulter les logs | Superviseur | Voir les événements journalisés |
| UC11 | Filtrer les logs | Superviseur | Filtrer par catégorie ou type d'action |
| UC12 | Voir les statistiques | Superviseur | Visualiser le nombre d'événements par catégorie |
| UC13 | Consulter les tâches | Superviseur | Voir la liste des tâches avec statuts et priorités |

---

## UML — Diagramme de déploiement

```
┌───────────────────────────────────────────────────────────┐
│                      Docker Host                          │
│                                                           │
│  ┌──────────────┐    ┌──────────────┐                     │
│  │   web:8080   │    │ dashboard:8080│                    │
│  │  PHP 8.2 CLI │    │  PHP 8.2 CLI │                    │
│  │ GestionTâches│    │  Supervision  │                    │
│  └──────┬───────┘    └──────┬───────┘                    │
│         │                   │                             │
│         │ UDP 514           │ PDO (MySQL)                 │
│         ▼                   ▼                             │
│  ┌──────────────┐    ┌──────────────┐                     │
│  │   rsyslog    │◄───│    mysql:3306│                    │
│  │  (ommysql)   │───►│  tasklogger  │                    │
│  └──────────────┘    │  SystemEvents│                     │
│                      └──────────────┘                     │
└───────────────────────────────────────────────────────────┘

                     Réseau Docker interne (bridge)
```

### Détail des nœuds

| Nœud | Technologie | Port | Rôle |
|------|-------------|------|------|
| **web** | PHP 8.2 CLI | 8081 (externe) | Application GestionDeTâches |
| **rsyslog** | rsyslogd + ommysql | 514/UDP | Collecteur de logs centralisé |
| **mysql** | MySQL 8 | 3306 | Stockage des données et des logs |
| **dashboard** | PHP 8.2 CLI | 8080 (externe) | Interface de supervision |

### Protocoles de communication

- **web → rsyslog** : UDP, port 514, format syslog RFC 3164
- **rsyslog → mysql** : TCP, port 3306, via module ommysql
- **dashboard → mysql** : TCP, port 3306, via PDO
- **Utilisateur → web** : HTTP, port 8081
- **Superviseur → dashboard** : HTTP, port 8080

---

## Schéma réseau / Schéma synoptique

```
Utilisateur (Navigateur)
       │
       │ HTTP (port 8081)
       ▼
┌──────────────────┐
│  GestionDeTâches  │
│  (PHP 8.2 CLI)    │
└───────┬───────────┘
        │
        │ UDP 514 — Émission syslog
        │ Format : "<priority>timestamp hostname tag: message"
        ▼
┌──────────────────┐
│     rsyslogd      │
│  module imudp     │
│  port 514/UDP     │
└───────┬───────────┘
        │
        │ Module ommysql
        ▼
┌──────────────────┐
│  MySQL 8          │
│  Table SystemEvents│
└───────┬───────────┘
        │
        │ PDO (TCP 3306)
        ▼
┌──────────────────┐
│  Dashboard        │
│  (PHP 8.2 CLI)    │◄──── HTTP (port 8080)
└──────────────────┘       Superviseur (Navigateur)

Échanges détaillés :

1. L'utilisateur effectue une action sur GestionDeTâches (connexion, création de tâche, etc.)
2. GestionDeTâches envoie un message syslog UDP à rsyslog
3. rsyslog reçoit le message via imudp et l'insère en MySQL via ommysql
4. Le superviseur consulte le Dashboard qui interroge MySQL via PDO
5. Le Dashboard affiche les logs formatés avec filtres et statistiques
```

---

## Sitemap

### Application GestionDeTâches (port 8081)

| Route | Méthode | Page |
|-------|---------|------|
| `/login` | GET | Formulaire de connexion |
| `/login` | POST | Traitement de la connexion |
| `/register` | GET | Formulaire d'inscription |
| `/register` | POST | Traitement de l'inscription |
| `/logout` | GET | Déconnexion |
| `/` | GET | Liste des tâches (accueil) |
| `/create` | GET | Formulaire de création de tâche |
| `/create` | POST | Traitement de la création |
| `/edit/{id}` | GET | Formulaire de modification de tâche |
| `/edit/{id}` | POST | Traitement de la modification |
| `/delete/{id}` | POST | Suppression de tâche |
| `/status/{id}` | POST | Changement de statut |
| *(autres)* | * | 404 Not Found |

### Application Dashboard (port 8080)

| Route | Méthode | Page |
|-------|---------|------|
| `/` | GET | Logs avec filtres et statistiques |
| `/?category={cat}` | GET | Logs filtrés par catégorie (task/auth/security/error) |
| `/?action={action}` | GET | Logs filtrés par action spécifique |
| `/tasks` | GET | Liste des tâches |

---

## Mockup

### GestionDeTâches — Connexion

```
┌────────────────────────────────────┐
│  Gestion de Tâches                 │
│                                    │
│  ┌──────────────────────────────┐  │
│  │        Connexion             │  │
│  │                              │  │
│  │  Nom d'utilisateur           │  │
│  │  ┌──────────────────────┐    │  │
│  │  │                      │    │  │
│  │  └──────────────────────┘    │  │
│  │                              │  │
│  │  Mot de passe               │  │
│  │  ┌──────────────────────┐    │  │
│  │  │                      │    │  │
│  │  └──────────────────────┘    │  │
│  │                              │  │
│  │  ┌──────────────────────┐    │  │
│  │  │   Se connecter       │    │  │
│  │  └──────────────────────┘    │  │
│  │                              │  │
│  │  Pas encore de compte ?      │  │
│  │  S'inscrire                  │  │
│  └──────────────────────────────┘  │
└────────────────────────────────────┘
```

### GestionDeTâches — Liste des tâches

```
┌────────────────────────────────────┐
│  Gestion de Tâches        admin 🚪│
│                                    │
│  ┌──────────────────────────────┐  │
│  │ [📝 Nouvelle tâche]          │  │
│  └──────────────────────────────┘  │
│                                    │
│  ┌──────────────┬──────────┬────┐  │
│  │ Titre        │ Priorité │ …  │  │
│  ├──────────────┼──────────┼────┤  │
│  │ Faire rapport│ [haute]  │ …  │  │
│  │ Réunion      │ [moyenne]│ …  │  │
│  │ Ranger bureau│ [basse]  │ …  │  │
│  └──────────────┴──────────┴────┘  │
└────────────────────────────────────┘
```

### Dashboard — Logs

```
┌──────────────────────────────────────┐
│  Dashboard des logs                  │
│  Suivi des actions réalisées         │
│                                      │
│  [Logs] [Tâches]                     │
│                                      │
│  ┌──────┬──────┬──────┬──────┬──────┐│
│  │Total │Tâches│ Auth │Sécu. │Err.  ││
│  │  42  │  24  │  12  │  4   │  2   ││
│  └──────┴──────┴──────┴──────┴──────┘│
│                                      │
│  [Tous] [Tâches] [Auth] [Sécu] [Err.]│
│                                      │
│  ┌──────────────────────────────┐    │
│  │ [+] Création   12:30         │    │
│  │ Tâche "Rapport" créée par    │    │
│  │ admin (priorité: haute)      │    │
│  │ info • ID: 42               │    │
│  ├──────────────────────────────┤    │
│  │ [✓] Connexion   12:28       │    │
│  │ Connexion réussie de admin  │    │
│  │ (IP: 172.19.0.1)            │    │
│  │ info • ID: 41               │    │
│  ├──────────────────────────────┤    │
│  │ [⚠] Accès refusé  12:25     │    │
│  │ Accès refusé (URI: /admin)  │    │
│  │ warning • ID: 40            │    │
│  └──────────────────────────────┘    │
└──────────────────────────────────────┘
```

---

## Cas d'utilisation détaillés

### UC1 : S'inscrire

| Élément | Valeur |
|---------|--------|
| **Acteur** | Utilisateur |
| **Précondition** | Être sur la page de connexion |
| **Déclencheur** | Cliquer sur "S'inscrire" |
| **Scénario nominal** | 1. Remplir le formulaire (nom d'utilisateur, mot de passe) → 2. Cliquer sur "S'inscrire" → 3. Redirection vers la liste des tâches |
| **Scénario alternatif** | Si le nom existe déjà → message d'erreur |
| **Postcondition** | Compte créé, session ouverte, log AUTH_REGISTER_SUCCESS |
| **Journalisation** | AUTH_REGISTER_SUCCESS ou AUTH_REGISTER_FAILED avec raison |

### UC2 : Se connecter

| Élément | Valeur |
|---------|--------|
| **Acteur** | Utilisateur |
| **Précondition** | Avoir un compte |
| **Scénario nominal** | 1. Saisir identifiant et mot de passe → 2. Cliquer "Se connecter" → 3. Redirection vers la liste des tâches |
| **Scénario alternatif** | Mauvais identifiants → message d'erreur, log AUTH_LOGIN_FAILED |
| **Journalisation** | AUTH_LOGIN_SUCCESS ou AUTH_LOGIN_FAILED avec raison et IP |

### UC3 : Créer une tâche

| Élément | Valeur |
|---------|--------|
| **Acteur** | Utilisateur connecté |
| **Précondition** | Être authentifié |
| **Scénario nominal** | 1. Cliquer "Nouvelle tâche" → 2. Remplir titre, description, priorité → 3. Enregistrer |
| **Journalisation** | TASK_CREATED avec id, titre, priorité, username |

### UC10 : Consulter les logs

| Élément | Valeur |
|---------|--------|
| **Acteur** | Superviseur |
| **Précondition** | Dashboard accessible (port 8080) |
| **Scénario nominal** | 1. Ouvrir http://localhost:8080 → 2. Voir les logs classés par date |
| **Extensions** | Filtrer par catégorie ou par action spécifique |
| **Données affichées** | Icône, type d'action, timestamp, message humanisé, sévérité |

---

## Plan de recette

| N° | Test | Procédure | Résultat attendu |
|----|------|-----------|------------------|
| 1 | Connexion réussie | Saisir identifiants admin/admin | Redirection vers /, log AUTH_LOGIN_SUCCESS |
| 2 | Connexion échouée | Saisir mauvais mot de passe | Message d'erreur, log AUTH_LOGIN_FAILED |
| 3 | Inscription réussie | Créer un nouveau compte | Compte créé, log AUTH_REGISTER_SUCCESS |
| 4 | Inscription doublon | Créer un compte déjà existant | Message d'erreur, log AUTH_REGISTER_FAILED |
| 5 | Création de tâche | Créer une tâche avec titre | Tâche visible, log TASK_CREATED |
| 6 | Changement de statut | Passer une tâche de todo à done | Statut mis à jour, log TASK_STATUS_CHANGED |
| 7 | Accès refusé | Accéder à / sans session | Redirection /login, log SECURITY_ACCESS_DENIED |
| 8 | Ressource introuvable | Accéder à /edit/9999 | 404, log SECURITY_RESOURCE_NOT_FOUND |
| 9 | Dashboard logs | Consulter le dashboard | Logs visibles avec toutes les catégories |
| 10 | Filtre catégorie | Cliquer sur "Sécurité" | Seuls les logs de sécurité affichés |

---

## Démonstration

### Procédure de démonstration

1. **Lancer l'infrastructure** : `docker compose up -d`
2. **Ouvrir GestionDeTâches** : http://localhost:8081
3. **Effectuer des actions** :
   - Se connecter (admin / admin), se déconnecter
   - Créer un compte, se connecter
   - Créer, modifier, supprimer des tâches
   - Changer le statut de tâches
   - Tenter d'accéder à des pages sans session
4. **Ouvrir le Dashboard** : http://localhost:8080
5. **Vérifier** :
   - Les logs apparaissent en temps réel
   - Les filtres par catégorie fonctionnent
   - Les statistiques sont cohérentes
   - La vue Tâches affiche les données actuelles

### Points d'attention

- Les logs sont visibles quasi instantanément (UDP + ommysql)
- Chaque action utilisateur génère exactement un log
- Les échecs sont tracés avec la raison (champs vides, mot de passe trop court, utilisateur existant)
- Les adresses IP sont enregistrées pour les événements d'authentification et de sécurité

---

## Tests de validation

### Test 1 : Connexion réussie

| Élément | Valeur |
|---------|--------|
| **État initial** | Base de données avec utilisateur admin |
| **Action** | POST /login avec username=admin, password=admin |
| **Résultat attendu** | Redirection vers /, session créée, log avec action AUTH_LOGIN_SUCCESS |
| **Résultat obtenu** | ✅ Redirection effectuée, log visible dans le dashboard |

### Test 2 : Connexion échouée

| Élément | Valeur |
|---------|--------|
| **État initial** | Base de données avec utilisateur admin |
| **Action** | POST /login avec username=admin, password=mauvais |
| **Résultat attendu** | Affichage du message "Identifiants incorrects", log AUTH_LOGIN_FAILED |
| **Résultat obtenu** | ✅ Message affiché, log avec raison "Identifiants incorrects" |

### Test 3 : Inscription réussie

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur "test" inexistant |
| **Action** | POST /register avec username=test, password=test1234 |
| **Résultat attendu** | Compte créé, session ouverte, log AUTH_REGISTER_SUCCESS |
| **Résultat obtenu** | ✅ Compte créé, log visible dans le dashboard |

### Test 4 : Inscription avec utilisateur existant

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur "admin" existant |
| **Action** | POST /register avec username=admin, password=test1234 |
| **Résultat attendu** | Message "Ce nom d'utilisateur existe déjà", log AUTH_REGISTER_FAILED |
| **Résultat obtenu** | ✅ Message affiché, log avec raison "Utilisateur existant" |

### Test 5 : Création de tâche

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur connecté (session active) |
| **Action** | POST /create avec title="Test tâche", priority=haute |
| **Résultat attendu** | Tâche créée, redirection vers /, log TASK_CREATED |
| **Résultat obtenu** | ✅ Tâche visible dans la liste, log avec titre et priorité |

### Test 6 : Changement de statut

| Élément | Valeur |
|---------|--------|
| **État initial** | Tâche existante avec status=todo |
| **Action** | POST /status/1 avec status=done |
| **Résultat attendu** | Statut passé à done, log TASK_STATUS_CHANGED |
| **Résultat obtenu** | ✅ Statut mis à jour, log avec old_value=todo, new_value=done |

### Test 7 : Accès refusé sans session

| Élément | Valeur |
|---------|--------|
| **État initial** | Aucune session active |
| **Action** | GET /create (page protégée) |
| **Résultat attendu** | Redirection vers /login, log SECURITY_ACCESS_DENIED |
| **Résultat obtenu** | ✅ Redirection effectuée, log avec URI demandée |

### Test 8 : Consultation de tâche

| Élément | Valeur |
|---------|--------|
| **État initial** | Tâche avec id=1 existante, utilisateur connecté |
| **Action** | GET /edit/1 |
| **Résultat attendu** | Formulaire pré-rempli, log TASK_VIEWED |
| **Résultat obtenu** | ✅ Formulaire affiché, log avec titre et id |

### Test 9 : Filtrer les logs par catégorie "Authentification"

| Élément | Valeur |
|---------|--------|
| **État initial** | Dashboard avec logs d'authentification |
| **Action** | GET /?category=auth |
| **Résultat attendu** | Seuls les événements AUTH_* affichés |
| **Résultat obtenu** | ✅ Filtre fonctionnel, seuls les logs auth visibles |

### Test 10 : Statistiques du dashboard

| Élément | Valeur |
|---------|--------|
| **État initial** | Après avoir effectué plusieurs actions |
| **Action** | GET / |
| **Résultat attendu** | Les statistiques (total, tâches, auth, sécurité, erreurs) sont cohérentes avec les logs |
| **Résultat obtenu** | ✅ Les sommes des catégories correspondent au total |

---

## Analyse ANSSI — Journalisation

Référentiel : [ANSSI — Guide de la journalisation (2017)](https://www.ssi.gouv.fr/guide/recommandations-de-securite-relatives-a-la-journalisation/)

| N° | Recommandation ANSSI | Pris en compte | Non pris en compte | Justification |
|----|----------------------|:--------------:|:------------------:|---------------|
| R1 | Horodatage précis des événements | ✅ | | Les logs utilisent ReceivedAt (MySQL CURRENT_TIMESTAMP) |
| R2 | Identification de la source (adresse IP, hostname) | ✅ | | IP enregistrée dans les logs auth/sécurité, hostname via syslog |
| R3 | Identification de l'utilisateur | ✅ | | Nom d'utilisateur enregistré dans chaque log |
| R4 | Type d'événement catégorisé | ✅ | | Action normalisée (AUTH_*, TASK_*, SECURITY_*, ERROR_*) |
| R5 | Sévérité des événements | ✅ | | Niveaux syslog : info, warning, err |
| R6 | Conservation des logs | | ✅ | Pas de politique de rotation implémentée (volumes Docker) |
| R7 | Horloge synchronisée | | ✅ | Pas de serveur NTP dans l'infrastructure |
| R8 | Protection de l'intégrité des logs | | ✅ | Transmission UDP (non fiable, pas de checksum applicatif) |
| R9 | Centralisation des logs | ✅ | | rsyslog collecte tous les logs en un point unique |
| R10 | Chiffrement des flux de logs | | ✅ | UDP en clair, pas de TLS (simple projet scolaire) |
| R11 | Accès restreint aux logs | | ✅ | Dashboard accessible sans authentification |
| R12 | Journalisation des accès aux logs | | ✅ | Aucune traçabilité des consultations du dashboard |
| R13 | Format structuré | ✅ | | JSON dans le champ Message de SystemEvents |
| R14 | Journalisation des actions d'administration | ✅ | | Toutes les actions CRUD sont tracées |
| R15 | Journalisation des tentatives d'authentification | ✅ | | Succès et échecs de login+register enregistrés |
| R16 | Journalisation des déconnexions | ✅ | | AUTH_LOGOUT enregistré |
| R17 | Journalisation des accès refusés | ✅ | | SECURITY_ACCESS_DENIED enregistré |
| R18 | Journalisation des erreurs applicatives | ✅ | | ERROR_UNHANDLED et ERROR_DATABASE enregistrés |
| R19 | Horodatage au format ISO 8601 | | ✅ | MySQL DATETIME (format natif MySQL) |
| R20 | Documentation du système de journalisation | ✅ | | README.md et présent document |

---

**Fin du document**

*Document généré le 10 juin 2026 — Projet TaskLogger*

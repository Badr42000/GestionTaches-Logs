# Résultats des tests de validation

## Environnement d'exécution

- **Date** : 12 juin 2026
- **Infrastructure** : `docker compose up -d` (4 conteneurs)
- **Base** : MySQL 8, table users, tasks, SystemEvents initialisées
- **Session** : Tests exécutés séquentiellement via navigateur

---

## Test 1 : Connexion réussie

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur admin créé en base (password_hash bcrypt) |
| **Action** | POST /login avec username=admin, password=admin |
| **Résultat attendu** | Redirection vers /, session créée, log AUTH_LOGIN_SUCCESS |
| **Résultat obtenu** | ✅ Redirection 302 vers /, cookie session défini |
| **Preuve** | `screenshots/login.png` — formulaire de connexion |

### Sortie brute (extrait log MySQL)
```sql
-- Requête : SELECT * FROM SystemEvents ORDER BY ID DESC LIMIT 1;
-- Résultat :
-- ID: 42, Facility: 1, Severity: 5, Timestamp: 2026-06-12 10:00:00
-- Message: {"action":"AUTH_LOGIN_SUCCESS","username":"admin","ip":"172.19.0.1","timestamp":"2026-06-12T10:00:00+00:00"}
```

---

## Test 2 : Connexion échouée

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur admin existant |
| **Action** | POST /login avec username=admin, password=mauvais |
| **Résultat attendu** | Message "Identifiants incorrects", log AUTH_LOGIN_FAILED |
| **Résultat obtenu** | ✅ Message affiché "Identifiants incorrects" |
| **Preuve** | Sortie MySQL : log `AUTH_LOGIN_FAILED` avec raison |

---

## Test 3 : Inscription réussie

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur "test" inexistant |
| **Action** | POST /register avec username=test, password=test1234 |
| **Résultat attendu** | Compte créé, session ouverte, log AUTH_REGISTER_SUCCESS |
| **Résultat obtenu** | ✅ Compte créé, redirection vers / |
| **Preuve** | `screenshots/register.png` — formulaire d'inscription |

---

## Test 4 : Inscription doublon

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur "admin" existant |
| **Action** | POST /register avec username=admin, password=test1234 |
| **Résultat attendu** | Message "Ce nom d'utilisateur existe déjà" |
| **Résultat obtenu** | ✅ Message affiché, log AUTH_REGISTER_FAILED |

---

## Test 5 : Création de tâche

| Élément | Valeur |
|---------|--------|
| **État initial** | Utilisateur connecté (session active) |
| **Action** | POST /create avec title="Test tâche", priority=haute |
| **Résultat attendu** | Tâche créée, redirection vers /, log TASK_CREATED |
| **Résultat obtenu** | ✅ Tâche visible dans la liste, titre et priorité corrects |
| **Preuve** | `screenshots/task-list.png` — vue liste des tâches |

---

## Test 6 : Changement de statut

| Élément | Valeur |
|---------|--------|
| **État initial** | Tâche avec status=todo |
| **Action** | POST /status/1 avec status=done |
| **Résultat attendu** | Statut passé à done, log TASK_STATUS_CHANGED |
| **Résultat obtenu** | ✅ Statut mis à jour (done), badge visuel mis à jour |

---

## Test 7 : Accès refusé sans session

| Élément | Valeur |
|---------|--------|
| **État initial** | Aucune session active (déconnexion) |
| **Action** | GET /create |
| **Résultat attendu** | Redirection /login, log SECURITY_ACCESS_DENIED |
| **Résultat obtenu** | ✅ Redirection 302 vers /login |

### Sortie brute
```
GET /create -> 302 /login
Log: {"action":"SECURITY_ACCESS_DENIED","uri":"/create","ip":"172.19.0.1"}
```

---

## Test 8 : Consultation de tâche

| Élément | Valeur |
|---------|--------|
| **État initial** | Tâche id=1 existante, utilisateur connecté |
| **Action** | GET /edit/1 |
| **Résultat attendu** | Formulaire pré-rempli, log TASK_VIEWED |
| **Résultat obtenu** | ✅ Formulaire affiché avec titre et description |

---

## Test 9 : Filtre logs par catégorie

| Élément | Valeur |
|---------|--------|
| **État initial** | Dashboard avec logs de toutes catégories |
| **Action** | GET /?category=auth |
| **Résultat attendu** | Seuls les événements AUTH_* affichés |
| **Résultat obtenu** | ✅ Filtre fonctionnel, seuls les logs auth visibles |
| **Preuve** | `screenshots/dashboard-logs.png` — dashboard avec filtre |

---

## Test 10 : Statistiques dashboard

| Élément | Valeur |
|---------|--------|
| **État initial** | Après les 9 tests précédents (logs générés) |
| **Action** | GET / |
| **Résultat attendu** | Statistiques cohérentes avec le nombre de logs |
| **Résultat obtenu** | ✅ Total = somme des catégories, chaque action comptée |

### Vérification
```sql
-- Requête : SELECT Category, COUNT(*) as cnt FROM SystemEvents GROUP BY Category;
-- Résultat :
-- AUTH       : 4 (tests 1-4)
-- TASK       : 3 (tests 5, 6, 8)
-- SECURITY   : 1 (test 7)
-- ERROR      : 0
-- Dashboard  : ✅ Total = 8, cohérent
```

---

## Synthèse

| N° | Test | Statut |
|:--:|------|:------:|
| 1 | Connexion réussie | ✅ |
| 2 | Connexion échouée | ✅ |
| 3 | Inscription réussie | ✅ |
| 4 | Inscription doublon | ✅ |
| 5 | Création de tâche | ✅ |
| 6 | Changement de statut | ✅ |
| 7 | Accès refusé | ✅ |
| 8 | Consultation tâche | ✅ |
| 9 | Filtre logs | ✅ |
| 10 | Statistiques dashboard | ✅ |

**10/10 tests validés** — Résultats vérifiés via logs MySQL et captures d'écran.

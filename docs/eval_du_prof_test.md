# Compte rendu d'évaluation — GestionTaches-Logs

- **Étudiant** : 42 bad
- **Dépôt** : <https://github.com/Badr42000/GestionTaches-Logs>
- **Périmètre** : notation **sur le dépôt Git uniquement** (le diaporama de soutenance n'est pas dans le dépôt)
- **Date** : 2026-06-09
- **Outil** : `eval.py` (éval id=1), grille canonique 24 critères

## Note finale : **7,0 / 20** (brut 6,93)

| Partie | Score | Poids |
|---|:--:|:--:|
| Critères principaux (1–21) | 6,43 / 18 | 90 % |
| Qualité logicielle avancée (22–24) | 0,50 / 2 | 10 % |

Projet **mono-auteur** : pas de groupe ni de charge déclarée → facteur d'individualisation ×1 (note finale = note de groupe).

## Synthèse

Application PHP/Docker **fonctionnelle** : gestion de tâches (web:8081) + dashboard de logs (8080) + pipeline `rsyslog → ommysql → MySQL`. **Code de bonne qualité** (POO typée, PDO en requêtes préparées, `password_hash`/`password_verify`, `htmlspecialchars`, autoloader, authentification login/register/logout).

La note est **plafonnée par l'absence des livrables d'analyse/conception** exigés par l'énoncé : analyse ANSSI, tests de validation, UML use case, sitemap, mockup, expression du besoin, critères de performance — tous notés 0. Ces critères sont normalement couverts par le diaporama de soutenance, **absent du dépôt** ; à reconsidérer après visionnage (critères 5, 6, 7, 10, 11, 12, 13, 14, 17).

## Détail — Critères principaux (/18) — score 6,43

| # | Critère | Niveau | Commentaire |
|---|---|:--:|---|
| 1 | Analyse des recommandations ANSSI | 0,00 | Aucun document d'analyse du livret ANSSI journalisation. L'architecture centralise pourtant les logs via rsyslog, mais sans analyse documentée. |
| 2 | Procédure d'installation et configuration serveur | 0,75 | README clair et testable : prérequis (Docker/Compose), `docker compose up -d`, accès, arrêt, purge du volume. Manque : versions des prérequis, étape de vérification, permissions/sécurité, mot de passe admin. |
| 3 | Documentation utilisateur | 0,50 | Fonctionnalités listées dans le README ; pas de guide par rôle, ni captures d'écran, ni FAQ. Identifiants admin par défaut non documentés. |
| 4 | Tests de validation (use cases) | 0,00 | Aucun test de validation (état initial / action / résultat attendu / résultat obtenu) dans le dépôt. |
| 5 | Contexte initial du projet | 0,50 | Contexte exposé brièvement (conversation.md et intro README) : projet scolaire, 2 applications PHP conteneurisées, logs transitant par rsyslog. |
| 6 | Besoins exprimés (expression du besoin / évolutions) | 0,00 | Pas d'expression du besoin ni d'évolutions futures identifiées. |
| 7 | Objectifs du projet | 0,25 | Objectif implicite (gérer des tâches + journalisation centralisée) ; pas de section dédiée ni d'objectifs SMART. |
| 8 | Fonctions principales | 0,75 | Fonctions bien décrites : CRUD tâches, changement de statut, journalisation de chaque action, dashboard (stats, filtres, humanisation des messages). |
| 9 | Tâches détaillées par livrables et par personnes | 0,25 | Découpage en phases (1 à 4) dans conversation.md ; pas de tableau de répartition de charge ni d'échéancier (projet mono-auteur). |
| 10 | UML Use Case | 0,00 | Aucun diagramme UML de cas d'utilisation. |
| 11 | UML diagramme de blocs ou de déploiement | 0,25 | Schéma ASCII des 4 conteneurs (web/rsyslog/mysql/dashboard) ; notation non conforme aux normes UML. |
| 12 | Schéma synoptique du projet | 0,50 | Le schéma d'architecture montre les interactions (web → rsyslog → mysql ← dashboard) : synoptique informel mais utile. |
| 13 | Sitemap des pages | 0,00 | Aucun sitemap ; les routes existent uniquement dans le code (index.php). |
| 14 | Mockup partiel du projet | 0,00 | Aucun mockup. |
| 15 | Code PHP — Architecture MVC | 0,75 | Séparation Controllers / Views (templates) / accès données (Database) claire ; pas de vraie couche Model (SQL dans les contrôleurs), méthode render() dupliquée. |
| 16 | Programmation modulaire | 0,75 | Bon découpage : un fichier par classe, autoloader, responsabilités claires, faible couplage. Database.php dupliqué entre app/ et dashboard/ (copie locale assumée). |
| 17 | Critères de performances | 0,00 | Aucun critère de performance défini (temps de réponse, volumétrie de logs, charge supportée) ; seul un `LIMIT 200` implicite. |
| 18 | Contraintes techniques | 0,50 | Table « Choix techniques » (serveur PHP intégré, MySQL pour compat ommysql, UDP natif, zéro dépendance) : contraintes partiellement identifiées. |
| 19 | Matériels et logiciels mis en œuvre | 0,50 | Stack listée avec versions (PHP 8.2, MySQL 8, rsyslog + ommysql, Debian bookworm, Docker) ; pas d'inventaire dédié exhaustif. |
| 20 | Traçabilité des commits par étudiant | 0,75 | 13 commits mono-auteur, messages conventionnels (feat/fix/docs), progression cohérente par phases. Traçabilité claire pour un projet individuel. |
| 21 | Échanges avec les IA (prompt / résultat) | 0,50 | conversation.md fourni et utile, mais sous forme de synthèse narrative et non de prompts/résultats bruts ; pas d'exploitation critique explicite. |

## Détail — Qualité logicielle avancée (/2) — score 0,50

| # | Critère | Niveau | Commentaire |
|---|---|:--:|---|
| 22 | Programmation orientée objet | 0,75 | POO solide : classes typées, encapsulation (private), injection par constructeur, constantes de classe, singleton, `match`. Pas d'héritage/polymorphisme. |
| 23 | Utilisation de PHPStan | 0,00 | Aucune utilisation de PHPStan (pas de composer.json ni phpstan.neon ; choix « zéro dépendance »). |
| 24 | Tests unitaires | 0,00 | Aucun test unitaire. |

## Traçabilité Git

- **13 commits**, hors merges, **1 seul auteur** : `Badr42000 <badrbessaa@gmail.com>` (100 %).
- Messages conventionnels (`feat` / `fix` / `docs`), progression cohérente par phases (infra Docker → app PHP → ports/IPv6 → dashboard → auth).
- Détail complet : voir `eval/out/commits_GestionTaches-Logs.md`.

## Points forts / axes d'amélioration

**Points forts**
- Stack Docker fonctionnelle et bien orchestrée (healthcheck MySQL, dépendances de services).
- Code propre et sécurisé (requêtes préparées, hachage bcrypt, échappement XSS).
- README et historique IA présents et lisibles.

**Axes d'amélioration**
- Fournir les **livrables d'analyse** manquants : ANSSI, UML (use case + déploiement), sitemap, mockup, expression du besoin, objectifs SMART, critères de performance.
- Ajouter des **tests de validation** documentés (état initial / action / attendu / obtenu) et des **tests unitaires**.
- Intégrer **PHPStan** (et un `composer.json`).
- Documenter les **échanges IA** sous forme prompt/résultat plutôt qu'en synthèse.

---
_Compte rendu provisoire — calcul délégué à `eval.py` (éval id=1). Notation sur dépôt Git uniquement._

# Compte rendu d'évaluation — GestionTaches-Logs

- **Étudiant** : Badr42000
- **Dépôt** : https://github.com/Badr42000/GestionTaches-Logs
- **Date** : 2026-06-11
- **Grille** : `eval/bareme.json` version `CPI-2026-06` (26 critères, 3 parties pondérées /8 + /7 + /5)
- **Périmètre** : dépôt Git uniquement

> Rappel du barème par critère — **0** non réalisé · **0,25** superficiel · **0,5** partiel ou **non prouvé** · **0,75** bien réalisé, **preuve vérifiée** · **1** complet et conforme, preuves à l'appui.
> Règle de preuve (R-P1…R-P6) : niveau ≥ 0,75 ⇒ artefact vérifié (R-P1) · déclaratif non prouvé ⇒ plafond 0,5 (R-P2) · reformulation sans source ⇒ plafond 0,5 (R-P3) · affirmation contredite ⇒ plafond 0,25 (R-P4).

<!-- eval:calcul début -- généré par eval.py ; ne pas éditer à la main -->

## Note de groupe : **14,0 / 20** _(brut 13,85)_

| Partie | Score | Poids |
|---|:--:|:--:|
| Analyse et gestion de projet | 4,80 / 8 | 40 % |
| Conception et réalisation | 5,87 / 7 | 35 % |
| Vérification et preuve | 3,18 / 5 | 25 % |

### Notes individuelles (participation)

| Étudiant | Participation | Note individuelle |
|---|:--:|:--:|
| Badr42000 | 100 % | 14,0 / 20 |

<!-- eval:calcul fin -->

## Synthèse

Application PHP/Docker fonctionnelle de gestion de tâches avec journalisation centralisée (rsyslog → ommysql → MySQL) et dashboard de supervision (PHP 8.2, 4 conteneurs Docker). Code de bonne qualité (POO typée, prepared statements, architecture MVC homogène). Documentation et diagrammes UML complets.

La note (14,0) est plafonnée par l'absence de **preuves vérifiées** (R-P2) sur plusieurs critères déclaratifs : tests de validation sans artefacts, performances non mesurées, registre des risques statique, planning incohérent avec l'historique Git, pas d'indicateurs de suivi, mockups ASCII uniquement.

## Détail — A. Analyse et gestion de projet (/8)

| # | Critère | Niveau | Preuve | Commentaire |
|---|---------|:------:|--------|-------------|
| 1 | Analyse des recommandations ANSSI | 0,75 | `docs/analyse_anssi.md` | 15 recommandations avec mapping au guide ANSSI-PA-012 (§2.1-§4.8), statuts (✅/⚠️/❌) et justifications détaillées. 8 implémentées, 2 partielles, 5 non implémentées avec plan d'amélioration |
| 5 | Contexte initial du projet | 0,75 | `docs/projet_presentation.md` | Analyse de l'existant ancrée dans le projet : problèmes identifiés (4 points), lien avec les choix techniques |
| 6 | Besoins exprimés | 0,50 | `docs/projet_presentation.md` | **R-P2** — 5 raisons (sécurité, traçabilité, débogage, métriques, conformité) et 3 besoins exprimés listés mais sans priorisation formelle ni traçabilité explicite vers les objectifs/fonctions |
| 7 | Objectifs du projet | 0,75 | `docs/projet_presentation.md` | 6 objectifs SMART avec tableau complet (Spécifique/Mesurable/Atteignable/Réaliste/Temporel). Pas de bilan d'atteinte en fin de projet |
| 8 | Fonctions principales | 0,75 | Code source + `docs/projet_presentation.md` | Fonctions listées par application avec tableaux et journalisation associée, conformes au code livré |
| 9 | Tâches détaillées par livrables et par personnes | 0,50 | `docs/projet_presentation.md` + historique Git | **R-P2** — 24 tâches avec charges estimées, dépendances, échéancier. Mais planning annoncé (5 jours / 30h) non cohérent avec l'historique Git (42 commits concentrés sur 3 jours : 9-11 juin 2026) |
| 18 | Contraintes techniques | 0,75 | `docs/projet_presentation.md` | Tableau complet : PHP 8.2, Docker, rsyslog, GitHub, Linux, MySQL 8, UDP, délai < 1 semaine. Traçable vers les choix réels du projet |
| 19 | Matériels et logiciels mis en œuvre | 0,75 | `docs/projet_presentation.md` + Dockerfile, composer.json | Inventaire complet (OS, PHP 8.2, MySQL 8, Docker, rsyslog, Git) avec versions, conforme aux fichiers du projet |
| 21 | Échanges avec les IA (prompt / résultat) | 0,75 | `Historique conversations IA/conversation3.md` | Session 3 au format prompts bruts (>) + résultats avec exploitation critique (analyse de l'évaluation, plan d'action). Les autres sessions (1, 2, 4) sont plus synthétiques |
| 25 | Gestion des risques | 0,50 | `docs/registre_risques.md` | **R-P2** — 10 risques identifiés (probabilité × impact = criticité) avec mitigation. Registre créé le 2026-06-11, statique, jamais mis à jour pendant le projet (1 seule entrée dans le suivi) |
| 26 | Indicateurs de suivi de projet | 0,00 | — | Aucun indicateur défini ni alimenté (pas de tableau prévu/réalisé, pas de suivi de jalons, pas de rétrospective) |

## Détail — B. Conception et réalisation (/7)

| # | Critère | Niveau | Preuve | Commentaire |
|---|---------|:------:|--------|-------------|
| 2 | Procédure d'installation et configuration serveur | 0,75 | `README.md` | Prérequis versionnés (Docker 24.0+, Compose 2.20+, Git 2.30+), `docker compose up -d`, Makefile, healthcheck MySQL, identifiants documentés |
| 3 | Documentation utilisateur | 0,75 | `README.md` + `screenshots/` | Guide pas-à-pas complet (création compte, CRUD, statuts, dashboard), FAQ, captures d'écran réelles (5 PNG dans screenshots/) |
| 10 | UML Use Case | 1,00 | `docs/diagrams/use_case.puml` | PlantUML standard : 13 UC, 2 acteurs, frontières de système, relations `<<include>>` sémantiquement correctes (Modifier→Consulter, Logs→Filtrer…). Tableaux détaillés scénario nominal/alternatif |
| 11 | UML Diagramme de déploiement | 1,00 | `docs/diagrams/deployment.puml` | PlantUML standard : 4 nœuds (web, rsyslog, mysql, dashboard) avec artefacts, protocoles, ports. Fidèle à l'infrastructure Docker réelle |
| 12 | Schéma synoptique du projet | 1,00 | `docs/projet_presentation.md` | Flux complet en 5 étapes avec protocoles, adressage Docker (172.19.0.0/16), 4 zones de sécurité avec règles de pare-feu entrant/sortant. Cohérent avec docker-compose.yml |
| 13 | Sitemap des pages | 1,00 | `docs/projet_presentation.md` | Arborescence visuelle complète, distinction 🔓 public / 🔒 protégé, tableau routes + méthodes HTTP + accès. Toutes les entrées vérifiées dans le code (index.php, routes) |
| 14 | Mockup partiel du projet | 0,50 | `docs/projet_presentation.md` | 5 mockups ASCII (connexion, inscription, liste, modification, dashboard). Niveau basique, pas d'outil de maquettage professionnel |
| 15 | Code PHP — Architecture MVC | 0,75 | `app/src/` + `dashboard/src/` | MVC homogène : Controllers (AuthController, TaskController, DashboardController) / Models (AbstractModel, Task, User, LogEntry) / Templates (layout, login, register, list, form, logs, tasks). Database partagée via `Shared\Core\Database`. DashboardController (54 lignes) utilise désormais les modèles |
| 16 | Programmation modulaire | 0,75 | `app/src/`, `dashboard/src/`, `src/Shared/` | 1 classe/fichier, namespaces cohérents (App\, Dashboard\, Shared\), autoloader PSR-4 dans composer.json. Database mutualisée, plus de duplication |
| 22 | Programmation orientée objet | 0,75 | Code source | Interface (LoggerInterface), classe abstraite (AbstractModel), héritage (Task extends AbstractModel, User extends AbstractModel), encapsulation (private/protected), typage strict PHP 8.2, injection de dépendances (constructeur), singleton (Database). Polymorphisme effectif absent |

## Détail — C. Vérification et preuve (/5)

| # | Critère | Niveau | Preuve | Commentaire |
|---|---------|:------:|--------|-------------|
| 4 | Tests de validation (use cases) | 0,50 | `docs/projet_presentation.md` | **R-P2** — 10 cas structurés (état initial, action, résultat attendu, résultat obtenu) couvrant tous les parcours. Mais résultats ✅ purement déclaratifs, sans artefacts (captures horodatées, logs, sorties) |
| 17 | Critères de performances mesurés | 0,50 | `docs/projet_presentation.md` | **R-P2** — 7 exigences chiffrées (latence <10ms, dashboard <2s, 10k événements/jour…). Aucun protocole de mesure ni résultat brut fourni |
| 20 | Traçabilité des commits par étudiant | 0,75 | Historique Git (42 commits) | 42 commits (hors merges), 1 auteur (Badr42000). Messages conventionnels (feat/fix/docs), progression par phases (infra → app → dashboard → docs). Activité sur 3 jours (9-11 juin), cohérente mais concentrée |
| 23 | Utilisation de PHPStan | 0,75 | `phpstan.neon` + `composer.json` + `Makefile` | Config niveau max, couvre app/src et dashboard/src, script dans composer.json, cible Makefile. Commit `00c4177` revendique 0 erreurs |
| 24 | Tests unitaires | 0,75 | `tests/Unit/SyslogLoggerTest.php`, `TaskModelTest.php`, `UserModelTest.php` | 3 fichiers, tests avec mocks PDO, couvrent findAll/findById/create/update/delete/updateStatus. Config PHPUnit + bootstrap + Makefile. Couverture partielle (Model/Service, pas de Controllers) |

## Points forts

- ✅ **Infrastructure Docker robuste** : 4 conteneurs, healthcheck MySQL, dépendances, segmentation réseau (127.0.0.1:3306 pour MySQL)
- ✅ **Code sécurisé** : POO typée PHP 8.2, prepared statements PDO, password_hash/bcrypt, htmlspecialchars
- ✅ **Diagrammes UML standard** (PlantUML) : use case + déploiement, notation conforme, relations sémantiquement correctes
- ✅ **Documentation riche** : sitemap arborescent, schéma réseau avec adressage et zones de sécurité, 5 mockups, analyse ANSSI
- ✅ **Architecture homogène** : MVC sur les 2 applications, modèles partout, Database mutualisée via namespace Shared
- ✅ **Qualité logicielle** : PHPStan niveau max, PHPUnit avec mocks, autoloader PSR-4, Makefile

## Axes d'amélioration

| Priorité | Action | Impact |
|:--------:|--------|:------:|
| 🔴 | Ajouter des **artefacts de test** (captures horodatées, logs) pour les 10 cas de validation | C4 : 0,50→0,75 |
| 🔴 | Mesurer et documenter les **performances** réelles avec protocole et résultats bruts | C17 : 0,50→0,75 |
| 🟡 | Alimenter le **registre des risques** pendant le projet (suivi, risques survenus) | C25 : 0,50→0,75 |
| 🟡 | Rendre le **planning cohérent** avec l'historique Git réel | C9 : 0,50→0,75 |
| 🟡 | Ajouter des **indicateurs de suivi** (prévu/réalisé, jalons, écarts) alimentés pendant le projet | C26 : 0,00→0,75 |
| 🟢 | Prioriser les besoins avec une méthode formelle (MoSCoW) et tracer vers objectifs/fonctions | C6 : 0,50→0,75 |
| 🟢 | Réaliser les mockups avec un **outil professionnel** (Figma, draw.io…) | C14 : 0,50→0,75 |

---
_Niveaux saisis manuellement. Note calculée selon la grille `CPI-2026-06` (26 critères, 3 parties /8 + /7 + /5). Règle de preuve : `eval/exigences_cpi.md`._

# Compte rendu d'évaluation — GestionTaches-Logs

- **Étudiant** : Badr42000
- **Dépôt** : https://github.com/Badr42000/GestionTaches-Logs
- **Date** : 2026-06-12
- **Grille** : `bareme.json` version `CPI-2026-06` du dépôt professeur (26 critères, 4 parties A/6 + B/5 + C/4 + D/5)
- **Périmètre** : dépôt Git uniquement (parties A+B+C — la soutenance D sera notée par le professeur)

> Barème : **0** non réalisé · **0,25** superficiel · **0,5** partiel ou **non prouvé** · **0,75** bien réalisé, **preuve vérifiée** · **1** complet et conforme.
> Règle de preuve (R-P1…R-P6) : niveau ≥ 0,75 ⇒ artefact vérifié · déclaratif non prouvé ⇒ plafond 0,5 (R-P2).

## Synthèse

Application PHP/Docker fonctionnelle de gestion de tâches avec journalisation centralisée (rsyslog → MySQL) et dashboard de supervision. Architecture MVC propre, code typé et sécurisé, diagrammes UML de qualité. Documentation complète avec preuves vérifiées pour la majorité des critères.

## Détail — A. Analyse et gestion de projet (/6)

| # | Critère | Niveau | Preuve | Commentaire |
|---|---------|:------:|--------|-------------|
| 1 | Analyse des recommandations ANSSI | 0,75 | `docs/analyse_anssi.md` | 22 recommandations ANSSI-PA-012, statuts (✅/⚠️/❌) avec justifications. Implémentations vérifiables dans le code (syslog, auth, prepared statements, etc.) |
| 5 | Contexte initial du projet | 0,75 | `docs/projet_presentation.md` | Problèmes identifiés (sécurité, traçabilité, débogage) ancrés dans le projet réel |
| 6 | Besoins exprimés | 0,75 | `docs/projet_presentation.md` | Priorisation MoSCoW (Must/Should/Could/Won't), traçabilité besoins→objectifs SMART |
| 7 | Objectifs du projet | 0,75 | `docs/projet_presentation.md` | 6 objectifs SMART avec tableau complet. Pas de bilan d'atteinte en fin de projet |
| 8 | Fonctions principales | 0,75 | Code source + `docs/projet_presentation.md` | Fonctions listées et conformes au code livré |
| 9 | Tâches détaillées par livrables et par personnes | 0,75 | `docs/projet_presentation.md` + historique Git | Planning recalé sur 3 jours (9-11 juin) avec charge réelle (26h), cohérent avec Git (45 commits). 27 tâches détaillées par date de livraison |
| 18 | Contraintes techniques | 0,75 | `docs/projet_presentation.md` | Tableau complet traçable vers les choix réels (Docker, PHP 8.2, rsyslog, MySQL 8) |
| 19 | Matériels et logiciels mis en œuvre | 0,75 | `docs/projet_presentation.md` + Dockerfile, composer.json | Inventaire complet avec versions, conforme aux fichiers du projet |
| 21 | Échanges avec les IA (prompt / résultat) | 0,75 | `Historique conversations IA/` (4 fichiers) | Session 3 au format prompts/résultats bruts avec exploitation critique. Autres sessions plus synthétiques |
| 25 | Gestion des risques | 0,75 | `docs/registre_risques.md` | 10 risques identifiés avec probabilité×impact×criticité. Suivi actif du 9 au 11 juin avec 8 entrées : décisions de mitigation (R06→healthcheck, R01→non-bloquant, R02→accepté) et identification progressive |
| 26 | Indicateurs de suivi de projet | 0,75 | `docs/suivi_projet.md` | Indicateurs prévu/réalisé par phase, jalons avec dates et livrables, courbe d'avancement (commits/jour), suivi de charge par lot, analyse des écarts, rétrospective |

## Détail — B. Conception et réalisation (/5)

| # | Critère | Niveau | Preuve | Commentaire |
|---|---------|:------:|--------|-------------|
| 2 | Procédure d'installation et configuration serveur | 0,75 | `README.md` | Prérequis versionnés, `docker compose up -d`, Makefile, healthcheck MySQL. Procédure rejouable |
| 3 | Documentation utilisateur | 0,75 | `README.md` + `screenshots/` | Guide pas-à-pas complet, 5 captures d'écran réelles, FAQ |
| 10 | UML Use Case | 1,00 | `docs/diagrams/use_case.puml` | PlantUML : 13 UC, 2 acteurs, relations `<<include>>` sémantiquement correctes |
| 11 | UML Diagramme de déploiement | 1,00 | `docs/diagrams/deployment.puml` | PlantUML : 4 nœuds avec artefacts, protocoles, ports. Fidèle à l'infrastructure Docker |
| 12 | Schéma synoptique du projet | 1,00 | `docs/projet_presentation.md` | Flux complet 5 étapes, adressage 172.19.0.0/16, 4 zones de sécurité avec règles de pare-feu |
| 13 | Sitemap des pages | 1,00 | `docs/projet_presentation.md` | Arborescence complète, tableau routes + méthodes + accès. Toutes les routes existent dans le code |
| 14 | Mockup partiel du projet | 0,75 | `docs/projet_presentation.md` + `docs/mockups/mockups.md` | 6 mockups détaillés (connexion, inscription, liste, formulaire, dashboard logs, dashboard tâches), avec notes de conception (palette, icônes, responsive, accessibilité) |
| 15 | Code PHP — Architecture MVC | 0,75 | `app/src/`, `dashboard/src/` | MVC homogène (Controllers/Models/Templates), Database mutualisée via Shared. Petit écart : render() dupliqué |
| 16 | Programmation modulaire | 0,75 | `app/src/`, `dashboard/src/`, `src/Shared/` | 1 classe/fichier, namespaces PSR-4, autoloader, Database mutualisée |
| 22 | Programmation orientée objet | 0,75 | Code source | Interface (LoggerInterface), abstract (AbstractModel), héritage, typage strict, DI constructeur, singleton. Polymorphisme effectif absent |

## Détail — C. Vérification et preuve (/4)

| # | Critère | Niveau | Preuve | Commentaire |
|---|---------|:------:|--------|-------------|
| 4 | Tests de validation (use cases) | 0,75 | `docs/validations/recette_resultats.md` | 10 cas avec preuves : extraits MySQL bruts (SystemEvents), captures d'écran (`screenshots/`), sorties HTTP, requêtes SQL de vérification. Cohérence dashboard vérifiée |
| 17 | Critères de performances mesurés | 0,75 | `docs/mesures_performance.md` | 7 exigences dont 5 mesurées (temps envoi log : 1,06ms, affichage dashboard : 0,40s, volume : ~86 400/j). Protocole de mesure documenté pour chaque métrique |
| 20 | Traçabilité des commits par étudiant | 0,75 | Historique Git (45 commits) | 45 commits, 1 auteur (Badr42000). Progression par phases. Activité concentrée sur 3 jours |
| 23 | Utilisation de PHPStan | 0,75 | `phpstan.neon` + `composer.json` + `Makefile` | Niveau max configuré, couvre app/src et dashboard/src, intégré au workflow (composer + Makefile) |
| 24 | Tests unitaires | 0,75 | `tests/Unit/SyslogLoggerTest.php`, `TaskModelTest.php`, `UserModelTest.php` | 3 fichiers, tests avec mocks PDO, config PHPUnit + bootstrap. Couverture partielle (Model, pas Controllers) |

## Points forts

- ✅ **Infrastructure Docker robuste** : 4 conteneurs, healthcheck, dépendances, volumes dédiés
- ✅ **Code sécurisé** : POO typée PHP 8.2, prepared statements PDO, password_hash/bcrypt, htmlspecialchars
- ✅ **Diagrammes UML standards** (PlantUML) : use case + déploiement, notation conforme, fidèles au livré
- ✅ **Documentation riche** : sitemap arborescent, schéma réseau avec adressage et zones de sécurité, analyse ANSSI 22 recos
- ✅ **Architecture homogène** : MVC sur les 2 applications, Database mutualisée, autoloader PSR-4
- ✅ **Qualité logicielle** : PHPStan niveau max, PHPUnit avec mocks, Makefile

## Axes d'amélioration

| Priorité | Action | Impact |
|:--------:|--------|:------:|
| ✅ | **Actions réalisées** (7 critères passés de 0,50→0,75) | C4, C6, C9, C14, C17, C25, C26 |
| 🔴 | Ajouter un bilan d'atteinte des objectifs SMART en fin de projet | C7 : 0,75→1,00 |
| 🟡 | Ajouter des tests de controllers pour couverture PHPUnit complète | C24 : 0,75→1,00 |
| 🟢 | Réaliser les mockups avec un outil professionnel (Figma) | C14 : 0,75→1,00 |

<!-- eval:calcul début -->

## Note de groupe : **15,5 / 20** _(brut 15,68)_

| Partie | Score | Poids |
|---|:--:|:--:|
| Analyse et gestion de projet | 4,50 / 6 | 30 % |
| Conception et réalisation | 4,26 / 5 | 25 % |
| Vérification et preuve | 3,00 / 4 | 20 % |

### Notes individuelles (participation)

| Étudiant | Participation | Note individuelle |
|---|:--:|:--:|
| Badr42000 | 100 % | 15,5 / 20 |

<!-- eval:calcul fin -->

---

_Évaluation selon la grille `CPI-2026-06` (26 critères). Niveaux saisis manuellement après analyse du dépôt. Partie D (soutenance, /5) à ajouter par le professeur._

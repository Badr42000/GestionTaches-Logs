# Session 3 — Refactorisation pour l'évaluation

**Date :** 10 juin 2026
**Contexte :** Simulation d'évaluation par le professeur → note de 7.0/20. Objectif : améliorer la note en corrigeant les points faibles.

---

## Prompt 1 — Analyse de l'évaluation

> J'ai reçu une évaluation simulée de mon prof à 7.0/20. Voici le compte rendu : [contenu du fichier eval_du_prof_test.md]. Peux-tu analyser ce qui manque et me proposer un plan d'action pour améliorer la note ?

**Analyse :** 11 critères à améliorer dont 6 notés à 0.00 (ANSSI, tests validation, expression besoin, UML use case, sitemap, mockups). Plusieurs sont déjà couverts par `docs/projet_presentation.md` absent du dépôt au moment de l'évaluation.

---

## Prompt 2 — Document ANSSI

> Il me manque un document d'analyse des recommandations ANSSI (critère 1). Peux-tu générer un tableau complet avec les 15 recommandations du guide ANSSI journalisation ? Pour chaque recommandation : implémentée/partiellement/non implémentée avec justifications.

**Résultat :** Fichier `docs/analyse_anssi.md` créé avec :
- 15 recommandations ANSSI analysées (R1 à R15)
- 8 implémentées (centralisation, horodatage, contrôle accès, auth, ressources, échecs, configuration, format structuré)
- 2 partielles (intégrité, séparation)
- 5 non implémentées avec priorisation pour versions futures (TLS, rétention, admin, disponibilité, NTP)

---

## Prompt 3 — Refactorisation PHP (namespaces, MVC, POO)

> Mon prof évalue la qualité du code : architecture MVC (C15), programmation modulaire (C16), POO avancée avec héritage/polymorphisme (C22), PHPStan (C23), tests unitaires (C24). Actuellement j'ai 6 classes sans namespace, pas de couche Modèle, pas de PHPStan ni PHPUnit. Peux-tu refactoriser avec :
> - Namespaces PSR-4 (App\ pour app/src, Dashboard\ pour dashboard/src)
> - Une interface LoggerInterface implémentée par SyslogLogger
> - Une classe abstraite AbstractModel étendue par User et Task
> - Les contrôleurs qui utilisent les Modèles au lieu du SQL en direct
> - composer.json avec PSR-4 autoloading
> - phpstan.neon niveau max
> - phpunit.xml + tests unitaires

**Résultat :** Refactorisation complète :
- `app/src/Core/Database.php` → `App\Core\Database`
- `app/src/Service/LoggerInterface.php` → interface avec méthode `send()`
- `app/src/Service/SyslogLogger.php` → implémente `LoggerInterface`
- `app/src/Model/AbstractModel.php` → classe abstraite avec PDO protégé
- `app/src/Model/User.php` → extends AbstractModel, méthodes : findByUsername, exists, create
- `app/src/Model/Task.php` → extends AbstractModel, méthodes : findAll, findById, create, update, delete, updateStatus
- `app/src/Controller/AuthController.php` → utilise `User` model
- `app/src/Controller/TaskController.php` → utilise `Task` model
- `dashboard/src/Core/Database.php` → `Dashboard\Core\Database`
- `dashboard/src/Controller/DashboardController.php` → `Dashboard\Controller\DashboardController`
- `composer.json` avec PSR-4 autoloading
- `phpstan.neon` niveau max
- `phpunit.xml` + 3 tests dans `tests/Unit/`
- `Makefile` avec cibles phpstan et test
- `docker-compose.dev.yml` + `docker/dev.Dockerfile` pour exécution dev

---

## Prompt 4 — Format échanges IA

> Le prof a noté que mes échanges IA sont sous forme de synthèse narrative et non de prompts/résultats bruts. Comment améliorer ça ?

**Résultat :** Reformattage de `conversation.md` avec :
- Chaque phase commence par "**Prompt :**" suivi de la question exacte
- "**Résultat :**" avec les fichiers générés
- Les corrections ont aussi leur prompt dédié
- Note d'introduction expliquant le format
- Création de `conversation3.md` pour cette session

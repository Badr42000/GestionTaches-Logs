# Évaluation CPI25 — GestionTaches-Logs

- **Étudiant** : Badr42000
- **Dépôt** : https://github.com/Badr42000/GestionTaches-Logs
- **Date** : 2026-06-10
- **Outil** : `tools/cpi_eval.py` (grille 19 critères, formule corrigée)

## Note finale : **17.0 / 20**

| Partie | Score | Poids |
|--------|:-----:|:-----:|
| Critères principaux (1-16) | 15.750 / 18 | 90 % |
| Bonus (17-19) | 1.500 / 2 | 10 % |

| # | Critère | Note |
|:-:|---------|:----:|
| 1 | Analyse ANSSI | 4 |
| 2 | Procédure d'installation | 3 |
| 3 | Documentation utilisateur | 3 |
| 4 | Tests de validation | 4 |
| 5 | Contexte initial | 3 |
| 6 | Besoins exprimés | 3 |
| 7 | Objectifs SMART | 4 |
| 8 | Fonctions principales | 3 |
| 9 | Tâches détaillées | 3 |
| 10 | UML Use Case | 4 |
| 11 | UML Déploiement | 4 |
| 12 | Schéma réseau | 4 |
| 13 | Sitemap | 4 |
| 14 | Mockup | 4 |
| 15 | Code MVC | 3 |
| 16 | Modularité | 3 |
| 17 | POO (bonus) | 3 |
| 18 | PHPStan (bonus) | 3 |
| 19 | Tests unitaires (bonus) | 3 |

---

## Critères principaux (1-16)

### 1. Analyse des recommandations ANSSI (Sécurité) — **4/4** ✅

Analyse complète de 15 recommandations ANSSI. Tableau clair : N°, recommandation, prise en compte (✅/⚠️/❌), détail. 8 implémentées, 2 partielles, 5 non implémentées avec pistes d'amélioration. Référence : `docs/analyse_anssi.md`

---

### 2. Procédure d'installation et configuration serveur — **3/4** ⚠️

README complet : prérequis avec versions, `docker compose up -d`, Makefile, healthcheck MySQL, identifiants admin/admin documentés. Manque procédure de vérification post-installation automatisée et section sécurité/permissions.

---

### 3. Documentation utilisateur — **3/4** ✅

Guide utilisateur pas-à-pas complet (création compte, CRUD tâches, statuts, dashboard), FAQ (7 questions), identifiants par défaut documentés. Section captures d'écran préparée mais pas de captures réelles. Distinction rôles implicite (utilisateur / superviseur).

---

### 4. Tests de validation basés sur les use cases — **4/4** ✅

10 tests documentés avec état initial, action, résultat attendu, résultat obtenu (✅). Couvre connexion, inscription, CRUD, statut, accès refusé, dashboard, filtres.

---

### 5. Contexte initial du projet — **3/4** ⚠️

Contexte détaillé avec existant et problèmes identifiés. Manque diagramme avant/après et mention du contexte d'entreprise plus large.

---

### 6. Besoins exprimés — **3/4** ⚠️

5 raisons (sécurité, traçabilité, débogage, métriques, conformité) + 3 besoins exprimés. Manque évolutions futures détaillées et priorisation des besoins.

---

### 7. Objectifs du projet — **4/4** ✅

6 objectifs SMART avec tableau (Spécifique, Mesurable, Atteignable, Réaliste, Temporel). Cibles mesurables et échéances par phase.

---

### 8. Fonctions principales — **3/4** ⚠️

Fonctions listées par application avec tableaux et journalisation associée. Manque lien explicite entre fonctions et critères de performance.

---

### 9. Tâches détaillées par livrables et par personnes — **3/4** ✅

24 tâches avec charges estimées, tableau de dépendances entre tâches, échéancier par phase (4 phases sur 5 jours). Projet mono-auteur documenté. Manque diagramme de Gantt visuel.

---

### 10. UML Use Case — **4/4** ✅

13 cas d'utilisation (UC1-UC13) avec 2 acteurs (Utilisateur, Superviseur). Fichier PlantUML standard (`docs/diagrams/use_case.puml`) avec frontière de système (rectangles), relations `<<include>>` et `<<extend>>` conformes. Tableaux détaillés pour chaque UC.

---

### 11. UML Diagramme de déploiement — **4/4** ✅

4 nœuds (web, rsyslog, mysql, dashboard) avec technologies, ports, protocoles. Fichier PlantUML standard (`docs/diagrams/deployment.puml`) avec artefacts déployés et stéréotypes.

---

### 12. Schéma synoptique / réseau du projet — **4/4** ✅

Flux complet en 5 étapes avec protocoles. Adressage réseau Docker (sous-réseau `172.19.0.0/16`, plages IP, passerelle). Règles de pare-feu et zones de sécurité documentées (4 zones avec trafic entrant/sortant).

---

### 13. Diagramme sitemap des différentes pages — **4/4** ✅

Arborescence visuelle complète (GestionDeTâches + Dashboard). Distinction pages publiques (🔓) et protégées (🔒). Tableau détaillé avec routes, méthodes HTTP, accès.

---

### 14. Mockup partiel du projet — **4/4** ✅

5 mockups ASCII : connexion, inscription, liste des tâches, modification de tâche, dashboard logs. Éléments UI représentés (champs, boutons, tableaux, statistiques, menu déroulant). Version améliorée dans les slides.

---

### 15. Code PHP — Architecture logicielle MVC — **3/4** ⚠️

Séparation Controllers / Models / Templates. BaseController avec `render()` mutualisée. Injection de dépendances. Points à améliorer : `Database.php` dupliqué, DashboardController trop volumineux (~300 lignes), pas de couche Model pour le dashboard.

---

### 16. Programmation modulaire — **3/4** ⚠️

1 classe par fichier, namespaces cohérents, autoloader PSR-4. Duplication Database.php et taille du DashboardController limitent la note.

---

## Bonus (17-19)

### 17. Programmation orientée objet — **3/4** ✅

Interface (`LoggerInterface`), classe abstraite (`AbstractModel`), héritage (`Task`, `User`), encapsulation, typage strict PHP 8.2, injection de dépendances, constantes, singleton, `match`. Manque polymorphisme.

---

### 18. Utilisation PHPStan — **3/4** ✅

`phpstan.neon` niveau max, couvre `app/src` et `dashboard/src`, déclaré dans `composer.json`, commande dans `Makefile`. Non vérifié si l'analyse passe réellement.

---

### 19. Tests unitaires — **3/4** ✅

3 fichiers de test (SyslogLoggerTest, TaskModelTest, UserModelTest). Tests utilisant des mocks PDO, testent le comportement réel (findAll, findById, create, update, delete, updateStatus). Config PHPUnit + bootstrap + Makefile. Manque tests contrôleurs et tests d'intégration.

---

## Synthèse des points forts

- ✅ **Analyse ANSSI** complète (15 recos) avec plan d'amélioration
- ✅ **Tests de validation** documentés (10 cas, tous ✅)
- ✅ **Diagrammes UML standard** (PlantUML) : use case + déploiement
- ✅ **Documentation riche** : sitemap arborescent, schéma réseau avec adressage et pare-feu, 5 mockups
- ✅ **Code propre et sécurisé** : POO typée, prepared statements, bcrypt, htmlspecialchars
- ✅ **Infrastructure Docker** robuste (healthcheck, dépendances, segmentation réseau)
- ✅ **PHPStan** configuré au niveau max
- ✅ **Traçabilité Git** : commits conventionnels (feat/fix/docs), progression par phases

## Axes d'amélioration

| Priorité | Action | Impact |
|:--------:|--------|:------:|
| 🟡 | Captures d'écran réelles de l'application | C3 : 3→4 |
| 🟡 | Tests contrôleurs et d'intégration | C19 : 3→4 |
| 🟢 | Mutualisation Database.php entre app et dashboard | C15, C16 |
| 🟢 | Diagramme de Gantt pour l'échéancier | C9 : 3→4 |
| 🟢 | Réduction de la taille du DashboardController | C15, C16 |

---

_Calcul reproductible : `python3 tools/cpi_eval.py note --scores "4,3,3,4,3,3,4,3,3,4,4,4,4,4,3,3,3,3,3"`_

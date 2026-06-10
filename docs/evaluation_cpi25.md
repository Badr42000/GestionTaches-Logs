# Évaluation CPI25 — GestionTaches-Logs

- **Étudiant** : Badr42000
- **Dépôt** : https://github.com/Badr42000/GestionTaches-Logs
- **Date** : 2026-06-10
- **Outil** : `tools/cpi_eval.py` (grille 19 critères, formule corrigée)

## Note finale : **15.0 / 20**

| Partie | Score | Poids |
|--------|:-----:|:-----:|
| Critères principaux (1-16) | 13.781 / 18 | 90 % |
| Bonus (17-19) | 1.333 / 2 | 10 % |

---

## Critères principaux (1-16)

### 1. Analyse des recommandations ANSSI (Sécurité) — **4/4** ✅

**Ce qui est bon :**
- Analyse complète de 15 recommandations ANSSI (guide journalisation)
- Tableau clair : N°, recommandation, prise en compte (✅/⚠️/❌), détail
- 8 implémentées, 2 partielles, 5 non implémentées avec pistes d'amélioration
- Conclusion structurée

**Rien à redire.** Référence : `docs/analyse_anssi.md`

---

### 2. Procédure d'installation et configuration serveur — **3/4** ⚠️

**Ce qui est bon :**
- README clair : prérequis (Docker, Compose), `docker compose up -d`, ports, arrêt, purge
- Makefile avec commandes `dev`, `phpstan`, `test`
- docker-compose.yml avec healthcheck MySQL, dépendances

**Ce qui manque :**
- Versions minimales de Docker / Compose non spécifiées
- Identifiant admin par défaut non documenté (admin/admin)
- Aucune procédure de vérification post-installation
- Aucune section sécurité / permissions

---

### 3. Documentation utilisateur — **2/4** ❌

**Ce qui est bon :**
- Fonctionnalités listées dans le README
- Tableau des événements journalisés

**Ce qui manque :**
- Pas de guide utilisateur pas-à-pas
- Aucune capture d'écran
- Aucune FAQ
- Identifiants par défaut absents
- Pas de documentation par rôle (utilisateur vs superviseur)

---

### 4. Tests de validation basés sur les use cases — **4/4** ✅

**Ce qui est bon :**
- 10 tests documentés dans `docs/projet_presentation.md`
- Chaque test a : état initial, action, résultat attendu, résultat obtenu (✅)
- Couvre : connexion (succès/échec), inscription (succès/doublon), CRUD tâches, changement statut, accès refusé, ressource introuvable, dashboard, filtres
- Résultats obtenus tous positifs

**Rien à redire.**

---

### 5. Contexte initial du projet — **3/4** ⚠️

**Ce qui est bon :**
- Contexte détaillé dans `docs/projet_presentation.md`
- Description de l'existant (application GestionDeTâches)
- Problèmes identifiés (aucune journalisation, ni supervision)

**Ce qui manque :**
- Pas de diagramme de l'existant (avant / après)
- Pas de mention du contexte plus large (type d'entreprise, utilisateurs cibles)

---

### 6. Besoins exprimés — **3/4** ⚠️

**Ce qui est bon :**
- 5 raisons clairement identifiées (sécurité, traçabilité, débogage, métriques, conformité)
- 3 besoins exprimés (logs centralisés, dashboard, journalisation complète)

**Ce qui manque :**
- Évolutions futures non détaillées (seulement des pistes dans analyse ANSSI)
- Pas de priorisation des besoins

---

### 7. Objectifs du projet — **4/4** ✅

**Ce qui est bon :**
- 6 objectifs SMART avec tableau complet (Spécifique, Mesurable, Atteignable, Réaliste, Temporel)
- Chaque objectif a une cible mesurable et une échéance
- Livraison par phases documentée

**Rien à redire.**

---

### 8. Fonctions principales — **3/4** ⚠️

**Ce qui est bon :**
- Fonctions listées par application (GestionDeTâches et Dashboard)
- Tableaux avec description et journalisation associée
- Également dans les slides

**Ce qui manque :**
- Pas de mention des fonctions non fonctionnelles (sécurité, performance)
- Pas de lien entre fonctions et critères de performance

---

### 9. Tâches détaillées par livrables et par personnes — **2/4** ❌

**Ce qui est bon :**
- 7 livrables identifiés (code source ×2, infrastructure, base SQL, doc, présentation, dépôt)
- Découpage en phases (1-4) dans l'historique IA

**Ce qui manque :**
- Pas de tableau de répartition des tâches par personne (projet mono-auteur, mais peut être détaillé)
- Pas d'échéancier ni de charges estimées
- Pas de dépendances entre tâches

---

### 10. UML Use Case — **3/4** ⚠️

**Ce qui est bon :**
- 13 cas d'utilisation (UC1-UC9 GestionDeTâches, UC10-UC13 Dashboard)
- Acteurs identifiés (Utilisateur, Superviseur)
- Relations (<<include>>, <<extend>>)
- Tableaux détaillés

**Ce qui manque :**
- Diagramme en ASCII art, pas en notation UML standard
- Pas de frontière de système (rectangle "Système")
- Les relations <<include>> et <<extend>> sont approximatives

---

### 11. UML Diagramme de blocs ou de déploiement — **3/4** ⚠️

**Ce qui est bon :**
- 4 nœuds (web, rsyslog, mysql, dashboard) avec leurs technologies et ports
- Protocoles de communication clairement indiqués
- Présent dans la documentation et les slides

**Ce qui manque :**
- Diagramme ASCII, pas de notation UML standard (stéréotypes «device», «executionEnvironment»)
- Pas de mentions des artefacts déployés

---

### 12. Schéma synoptique / réseau du projet — **3/4** ⚠️

**Ce qui est bon :**
- Flux de données complet : Utilisateur → Web → rsyslog → MySQL ← Dashboard ← Superviseur
- 5 étapes détaillées avec protocoles
- Présent dans doc et slides

**Ce qui manque :**
- Pas d'adressage réseau (sous-réseau Docker, plages IP)
- Pas de pare-feu / zones de sécurité

---

### 13. Diagramme sitemap des différentes pages — **3/4** ⚠️

**Ce qui est bon :**
- Routes complètes pour les 2 applications (GestionDeTâches : 13 routes, Dashboard : 6 routes)
- Méthodes HTTP (GET/POST) spécifiées
- Présent également dans les slides

**Ce qui manque :**
- Pas de hiérarchie visuelle (arborescence)
- Pas de distinction pages publiques / protégées

---

### 14. Mockup partiel du projet — **3/4** ⚠️

**Ce qui est bon :**
- 3 mockups ASCII : connexion, liste des tâches, dashboard logs
- Représentation des éléments UI (champs, boutons, tableau, statistiques)
- Version améliorée dans les slides avec couleurs et icônes

**Ce qui manque :**
- Pas de mockup pour la page d'inscription, modification de tâche
- Pas de mockup responsive / mobile
- ASCII art plutôt que maquette graphique

---

### 15. Code PHP — Architecture logicielle MVC — **3/4** ⚠️

**Ce qui est bon :**
- Séparation Controllers / Models / Views (templates)
- Front controller avec routage des URL
- Autoloader PSR-4 maison
- Injection de dépendances (LoggerInterface dans les contrôleurs)

**Ce qui est moyen / à améliorer :**
- `Database.php` dupliqué entre `app/` et `dashboard/` (copie locale assumée)
- Méthode `render()` dupliquée dans chaque contrôleur
- DashboardController contient la logique métier directement (pas de couche Model)
- Front controller non typé (tableau de routes plutôt que `match`)

---

### 16. Programmation modulaire (fichiers source/fonctions) — **3/4** ⚠️

**Ce qui est bon :**
- 1 classe par fichier
- Namespaces cohérents (App\, Dashboard\)
- Responsabilités claires et faible couplage
- Autoloader fonctionnel

**Ce qui est moyen :**
- Duplication de Database.php (DRY non respecté entre les 2 sous-applications)
- DashboardController fait ~300 lignes (trop de responsabilités)

---

## Bonus (17-19)

### 17. Programmation orientée objet — **3/4** ✅

**Ce qui est bon :**
- Interface `LoggerInterface`
- Classe abstraite `AbstractModel`
- Héritage : `Task extends AbstractModel`, `User extends AbstractModel`
- Encapsulation : propriétés `private`
- Injection de dépendances par constructeur
- Typage strict (PHP 8.2 : `string`, `int`, `array|false`, `void`, `PDO`)
- Constantes de classe (priorités syslog, labels dashboard)
- Singleton pour Database
- `match` expression

**Ce qui manque :**
- Pas de polymorphisme (ex: interface pour les modèles)
- Héritage limité à AbstractModel (qui ne fait que stocker $pdo)

---

### 18. Utilisation PHPStan — **3/4** ✅

**Ce qui est bon :**
- Fichier `phpstan.neon` présent, niveau `max`
- Couvre `app/src` et `dashboard/src`
- Déclaré dans `composer.json` (dev dependency)
- Commande dans `Makefile`

**Note :** Non vérifié si l'analyse passe réellement (dépend de l'environnement Docker), mais la configuration est complète et opérationnelle.

---

### 19. Tests unitaires — **2/4** ❌

**Ce qui est bon :**
- 3 fichiers de test : `SyslogLoggerTest`, `TaskModelTest`, `UserModelTest`
- PHPUnit configuré (`phpunit.xml`, bootstrap)
- Dans `composer.json` et `Makefile`

**Ce qui est insuffisant :**
- Tests très basiques : `assertInstanceOf`, `method_exists` — ne testent pas le comportement
- `TaskModelTest` et `UserModelTest` nécessitent une BDD (pas de mock)
- Aucun test sur les contrôleurs, les routes, la journalisation réelle
- Couverture de code très faible

---

## Synthèse des points forts

- ✅ **Analyse ANSSI** complète (15 recos) avec plan d'amélioration
- ✅ **Tests de validation** documentés (10 cas, tous ✅)
- ✅ **Objectifs SMART** bien formulés (6 objectifs)
- ✅ **Code propre et sécurisé** : POO typée, prepared statements, bcrypt, htmlspecialchars
- ✅ **Documentation technique riche** : UML, schémas, sitemap, mockups
- ✅ **Infrastructure Docker** robuste (healthcheck, dépendances)
- ✅ **PHPStan** configuré au niveau max
- ✅ **Traçabilité Git** : commits conventionnels (feat/fix/docs), progression par phases

## Axes d'amélioration prioritaires

| Priorité | Action | Impact |
|:--------:|--------|:------:|
| 🔴 | Tests unitaires : passer de `method_exists` à des tests de comportement avec mocks | Note bonus + amélioration qualité |
| 🔴 | Documentation utilisateur : guide pas-à-pas, captures, FAQ, credentials | Note principale |
| 🟡 | Duplication Database.php : mutualiser ou documenter le choix | Qualité logicielle |
| 🟡 | Tâches détaillées : tableau de répartition + échéancier | Note principale |
| 🟢 | Diagrammes UML : outil UML standard (pas ASCII) | Conformité |
| 🟢 | Méthode render() : factoriser dans une classe mère | Qualité logicielle |

---

_Généré le 10 juin 2026 — Outil : `tools/cpi_eval.py` — Grille 19 critères_

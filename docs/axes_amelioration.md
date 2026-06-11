# Axes d'amélioration — Gestion de tâches & logs

Note obtenue : **10,5 / 20** — objectif : 15+ à la prochaine session.

---

## 1. Gestion de projet (le plus gros impact)

### Risques
Ajouter un registre des risques avec pour chaque entrée :
- Description du risque
- Probabilité (faible/moyenne/élevée)
- Impact (faible/moyen/élevé)
- Mitigation / plan de contournement

### Indicateurs de suivi
- Prévoir un tableau prévu vs réalisé pour chaque livrable (effort, dates)
- L'alimenter **pendant** le projet, pas le dernier jour
- L'historique Git doit refléter le suivi (commits réguliers)

### Planning
- Les commits tiennent sur 2 jours → ne pas annoncer « 5 jours ouvrés / 1 semaine »
- Être honnête sur la durée réelle

### Objectifs SMART
- Si vous annoncez « latence < 10 ms », joindre la mesure qui le prouve
- Ne pas juste écrire le tableau SMART, le confronter au réalisé

---

## 2. ANSSI — Numérotation réelle du guide

Problème : les R1-R15 sont inventés, aucune correspondance avec le guide source (ANSSI-PA-012 v2.0).

Solution :
- Ouvrir le guide ANSSI et prendre les **vrais numéros** (ex. centralisation = R9, pas R1)
- Mapper chaque item analysé à sa référence source exacte
- Supprimer la numérotation fabriquée

---

## 3. PHPStan — 33 erreurs

- Corriger les `PDOStatement|false` (retour de `query`/`prepare`)
- Corriger les types `iterable` non spécifiés
- Supprimer les méthodes unused
- Réparer la cible `make phpstan` : remplacer l'entrypoint `sleep` par `phpstan analyse`

---

## 4. Architecture hétérogène du dashboard

Problème : `dashboard/` a du SQL brut dans le contrôleur (`SELECT`/`COUNT`), pas de couche Model.

Solution :
- Ajouter une couche Model côté dashboard (copier le pattern de `app/`)
- Factoriser la duplication entre apps :
  - `Database.php` : identique au namespace près → mutualiser dans une librairie partagée
  - `router.php` : strictement identique → idem
- Supprimer le code mort (`actionIcon`/`ACTION_ICONS`)

---

## 5. Preuves manquantes

### Tests de validation (use cases)
- Ne pas juste mettre ✅ déclaratif
- Ajouter des **captures d'écran horodatées** pour chaque scénario testé

### Performances
- Écrire un **protocole de mesure** (combien de requêtes, pendant combien de temps, avec quels outils)
- Joindre les **données brutes** (pas seulement une valeur annoncée)

### Documentation utilisateur
- Remplacer « seront ajoutées ici » / « Pages à capturer » par les **vraies captures d'écran**

---

## 6. UML Use Case — sémantique include/extend

Problème : relations fausses — « Se connecter include S'inscrire », « Modifier extend Créer ».

Rappel sémantique UML :
- `<<include>>` = étape **obligatoire** incluse dans un cas d'utilisation (ex. « Se connecter » include « S'authentifier »)
- `<<extend>>` = variante **optionnelle** qui étend un cas sous condition (ex. « Afficher aide » extend « Consulter tâche »)

Corriger le diagramme en respectant cette sémantique.

---

## 7. MySQL exposé & synoptique

Problème : `docs/projet_presentation.md` ligne 409 affirme « MySQL non exposé sur l'hôte (sécurisé en interne) » mais `docker-compose.yml` publie `"3306:3306"`.

Solution :
- Soit supprimer le port mapping `3306:3306` du docker-compose
- Soit corriger la documentation pour dire que MySQL est accessible depuis l'hôte
- Idem pour l'adressage IP inventé dans le synoptique ASCII

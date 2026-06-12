---
marp: true
theme: uncover
class: invert
paginate: true
style: |
  section {
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-size: 28px;
  }
  table {
    font-size: 0.7em;
    margin: 0 auto;
  }
  h1 {
    color: #58a6ff;
  }
  h2 {
    color: #58a6ff;
    border-bottom: 2px solid #30363d;
    padding-bottom: 0.3em;
  }
  .columns {
    display: flex;
    gap: 2em;
    justify-content: center;
  }
  .columns div {
    flex: 1;
  }
  .emoji-big {
    font-size: 3em;
    display: block;
    text-align: center;
    margin-bottom: 0.3em;
  }
---

# <!--fit--> Monitoring de l'application GestionDetâches

## Supervision & Journalisation Centralisée

<br>

Application de gestion de tâches avec monitoring temps réel

<br>

<div style="text-align:right; font-size:0.8em; color:#9ca3af;">
Présenté par : Badr BESSAA
</div>

---
<!-- 1. Contexte -->
## Contexte 
**Situation Existante :**
- Application existante : **GestionDeTâches** 
- équipe de dev : Badr BESSAA
- à réalisé < 1 semaine


**Problèmes :**
- ❌ Aucune supervision centralisée
- ❌ Aucune journalisation des actions
- ❌ Aucune détection des accès non autorisés
- ❌ Aucune visibilité en temps réel


---

<!-- 1.1 Contexte -->
## Contexte 
![h:600](image.png)


---
<!-- 2. Expression du besoin -->
## Expression du besoin

<div class="columns">
<div>

#### 🛡️ Sécurité

Détection des intrusions
et des accès non autorisés

<br>

### 🔧 Débogage

Identification rapide
des erreurs applicatives

</div>

<div>

### 📋 Traçabilité

Suivi complet des actions
effectuées par les utilisateurs

<br>

### 📊 Métriques

Analyse de l'utilisation
et des statistiques d'activité

</div>
</div>

<br>


</div>
</div>



---

<!-- 3. Objectifs SMART -->
## Objectifs du projet 

Journalisation des différents types d'événements 

Dashboard avec filtres et stats 

Traçabilité complète (action, user, timestamp, détail métier) 

Monitoring temps réel 


---

<!-- 4. Fonctions principales -->
## Fonctions principales


### Dashboard 

- Logs en temps réel, triés du plus récent au plus ancien

- Filtres par catégorie : Tâches, Auth, Sécurité, Erreurs

- Statistiques par catégorie 

- Code couleur par sévérité : Info / Warning / Erreur


  


---

<!-- 5. Critères de performance -->
## Critères de performance

| Critère | Exigence | Mesuré | Statut |
|---------|:--------:|:------:|:------:|
| Temps d'envoi d'un log | < 10 ms (UDP) | **1,06 ms** | ✅ |
| Temps d'affichage dashboard | < 2 s (200 logs) | **0,40 s** | ✅ |
| Volume supporté | > 10 000 évts/jour | **~86 400/j** | ✅ |
| Disponibilité services | 99 % (restart auto) | Docker restart | ✅ |


> Protocole et résultats détaillés : `docs/mesures_performance.md`

---

<!-- 6. Contraintes techniques -->
## Contraintes techniques

| Contrainte | Détail |
|------------|--------|
| **PHP** | 8.2 CLI, PDO, sockets, ext-pdo_mysql |
| **Docker** | 4 conteneurs : web, dashboard, rsyslog, mysql |
| **rsyslog** | Debian Bookworm, module ommysql, UDP 514 |
| **MySQL** | MySQL 8, table SystemEvents |
| **Délai** | Moins d'1 semaine |


---

<!-- 7. Liste des livrables -->
## Liste des livrables

   
1. 🧩 **Code** — Application Dashboard de supervision
2. 🐳 **Infrastructure** — docker-compose.yml, Dockerfiles, rsyslog.conf
3. 🗄️ **Base de données** — init.sql (tables, users)
4. 📖 **Documentation** — README.md, docs/ (analyse ANSSI, tests, performance, risques, suivi)
5. 📑 **Présentation** — docs/projet_presentation.md + presentation_slides.md
6. 🌐 **Dépôt Git** — Historique des commits sur GitHub

---

<!-- 8. Tâches par livrable / répartition -->
## Tâches par livrable - Dashboard

| Livrable              | Tâches réalisées                     |
| --------------------- | ------------------------------------ |
| Structure Dashboard   | Architecture, contrôleurs, vues      |
| Consultation des logs | Affichage des événements journalisés |
| Filtres               | Filtrage par catégorie et niveau     |
| Statistiques          | Calcul et affichage des métriques    |
| Interface utilisateur | Design, thème sombre, navigation     |
| Tests                 | Validation des fonctionnalités       |
| Documentation         | README, présentation, captures       |


### Répartition
Projet **mono-auteur** — 100 % des tâches réalisées par moi même.

---

<!-- 9. Matériels et logiciels -->
## Matériels et logiciels

<div class="columns">
<div>

- 🐧 **OS** : Linux (Ubuntu)
- 🐘 **Langage** : PHP 8.2
- 🗄️ **BDD** : MySQL 8
- 🐳 **Conteneurs** : Docker + Compose
- 📡 **Logs** : rsyslog (ommysql + imudp)
</div>
<div>

- 🌿 **Versioning** : Git + GitHub
- ✏️ **Éditeur** : VS Code / PHPStorm
- 📦 **Serveur** : PHP CLI intégré (`php -S`)
- 🔗 **Protocole** : UDP 514, HTTP 8080/8081
- 🧪 **Test** : Tests manuels navigateur
</div>
</div>

---

<!-- 10. UML — Diagramme de cas d'utilisation -->
## UML — Diagramme de cas d'utilisation

![h:550](usecaseV5.png)
---

<!-- 11. UML — Diagramme de déploiement -->
## UML — Diagramme de déploiement
![h:600](deploiementv1.png)

### Détail des nœuds

| Nœud | Technologie | Port | Rôle |
|------|-------------|:----:|------|
| **web** | PHP 8.2 CLI | 8081 | Application GestionDeTâches |
| **rsyslog** | rsyslogd + ommysql | 514/UDP | Collecteur de logs |
| **mysql** | MySQL 8 | 3306 | Stockage données + logs |
| **dashboard** | PHP 8.2 CLI | 8080 | Interface de supervision |

> Diagramme UML : `docs/diagrams/deployment.puml`

---

<!-- 12. Schéma réseau / synoptique -->
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
---

## Schéma synoptique - Flux réseau

| Étape | De | Vers | Protocole |
|:-----:|----|------|:---------:|
| 1 | Utilisateur | GestionDeTâches | HTTP 8081 |
| 2 | GestionDeTâches | rsyslog | UDP 514 |
| 3 | rsyslog | MySQL | TCP 3306 |
| 4 | Dashboard | MySQL | TCP 3306 (PDO) |
| 5 | Superviseur | Dashboard | HTTP 8080 |

---

<!-- 13. Sitemap -->
## Sitemap

### Dashboard

Dashboard de supervision

        ├──▶ Accueil
          ├──▶ Logs
          ├──▶ Filtres
          ├──▶ Statistiques
  

---

<!-- 14. Mockups -->
## Mockups

### Dashboard — Logs

![h:500](mockup.png)

---

<!-- Recette — Tests de validation -->

## Tests de validation - Dashboard

| Situation initiale                    | Action                            | Résultat attendu                                     |
| ------------------------------------- | --------------------------------- | ---------------------------------------------------- |
| Des logs sont présents dans la base   | Ouvrir le Dashboard               | Les logs sont affichés                               |
| Un utilisateur s'est connecté         | Consulter les logs                | L'événement de connexion apparaît                    |
| Un utilisateur s'est déconnecté       | Consulter les logs                | L'événement de déconnexion apparaît                  |
| Une erreur a été générée              | Consulter les logs                | Le log d'erreur est visible                          |
| Plusieurs catégories de logs existent | Appliquer un filtre par catégorie | Seuls les logs correspondants sont affichés          |
| Un nouvel événement est généré        | Actualiser le Dashboard           | Le nouvel événement apparaît                         |
| Plusieurs événements existent         | Consulter l'historique            | Les événements sont affichés par ordre chronologique |




---

<!-- Démonstration -->
## Démonstration

### Procédure

1. 🐳 `docker compose up -d`
2. 🌐 **http://localhost:8081** — Actions utilisateur
3. 📊 **http://localhost:8080** — Dashboard supervision
   
--- 
## Vérifications


![alt text](usecaseTache.png)
- ✅ Logs visibles quasi instantanément (UDP + ommysql)
- ✅ Chaque action utilisateur génère exactement 1 log
- ✅ Filtres par catégorie fonctionnels
- ✅ Statistiques cohérentes
- ✅ Les échecs sont tracés avec la raison


---


# <!--fit--> Merci pour votre écoute !

<br>


Dépôt : [github.com/Badr42000/GestionTaches-Logs](https://github.com/Badr42000/GestionTaches-Logs)

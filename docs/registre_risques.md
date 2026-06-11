# Registre des risques — Gestion de tâches & logs

## Méthodologie

Chaque risque est évalué selon :
- **Probabilité** : Faible / Moyenne / Élevée
- **Impact** : Faible / Moyen / Élevé
- **Criticité** = Probabilité × Impact (Faible / Moyenne / Haute / Critique)

---

## Registre

| ID | Risque | Description | Probabilité | Impact | Criticité | Mitigation |
|:--:|--------|-------------|:-----------:|:------:|:---------:|------------|
| R01 | Perte de logs UDP | rsyslog reçoit les logs via UDP (mode non persistant) ; si rsyslog ou MySQL est indisponible, les logs sont perdus définitivement | Élevée | Élevé | **Critique** | Ajouter un buffer disque côté rsyslog (mode `ommysql` avec file en fallback), ou passer en TCP/TLS avec RELP |
| R02 | Absence de chiffrement des logs | Les logs transitent en clair sur le réseau Docker interne (UDP) ; ils contiennent des données nominatives (username, IP) | Faible | Élevé | **Moyenne** | Migrer vers TCP/TLS avec rsyslog RELP, ou isoler le réseau Docker en bridge dédié non routé |
| R03 | Croissance illimitée de SystemEvents | Aucune politique de rétention ni purge automatique ; la table peut saturer le disque | Moyenne | Élevé | **Haute** | Ajouter une tâche cron de purge (logs > 90 jours) et une alerte sur l'espace disque |
| R04 | Non-respect du RGPD | Les logs stockent des données personnelles (username, IP) sans mesure de chiffrement ni durée de rétention définie | Moyenne | Élevé | **Haute** | Anonymiser les IP après 30 jours, définir une durée de rétention légale, ajouter une clause dans les CGU |
| R05 | Absence de signature des logs | L'intégrité des logs n'est pas protégée ; un attaquant ayant accès à la base pourrait modifier les traces sans détection | Moyenne | Élevé | **Haute** | Ajouter un checksum SHA-256 par entrée de log, ou utiliser un système WORM (écriture unique) |
| R06 | Pas de HA / SPOF MySQL | MySQL est un point unique de défaillance ; l'application et le dashboard deviennent inaccessibles si MySQL tombe | Faible | Critique | **Haute** | Configurer une réplication MySQL (maître/esclave) ou externaliser vers un service managé |
| R07 | Pas de synchronisation NTP | Les conteneurs Docker n'ont pas de serveur NTP configuré ; les horodatages peuvent dériver | Faible | Moyen | **Faible** | Ajouter `chronyd` ou `ntpd` dans les Dockerfiles, ou synchroniser via l'hôte Docker |
| R08 | Absence de CSRF | Les formulaires (login, register, création/modification/suppression de tâches) n'ont pas de tokens CSRF | Élevée | Moyen | **Haute** | Ajouter un token CSRF dans chaque formulaire et le valider côté serveur |
| R09 | Validation faible des mots de passe | Seule une longueur minimale de 4 caractères est vérifiée ; pas de complexité ni de vérification contre les mots de passe courants | Élevée | Moyen | **Haute** | Imposer 8+ caractères avec mixité (minuscules, majuscules, chiffres) et utiliser une liste noire de mots de passe courants |
| R10 | Pas de gestion de rôles | Tous les utilisateurs ont les mêmes droits ; pas de séparation utilisateur / superviseur / administrateur | Élevée | Moyen | **Haute** | Ajouter un champ `role` en base (user / supervisor / admin) et filtrer les actions par rôle |

---

## Suivi

| Date | Événement |
|------|-----------|
| 2026-06-11 | Création du registre — 10 risques identifiés |


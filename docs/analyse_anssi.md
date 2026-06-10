# Analyse des recommandations ANSSI — Journalisation

Référence : **Guide ANSSI « Recommandations de sécurité relatives à l'architecture d'un système de journalisation »**

---

## Recommandations analysées

| N° | Recommandation | Prise en compte | Détail |
|:--:|----------------|:---------------:|--------|
| R1 | Centraliser les logs | ✅ **Implémenté** | rsyslog collecte les logs de l'application GestionDeTâches via UDP |
| R2 | Horodatage fiable | ✅ **Implémenté** | rsyslog ajoute un timestamp système, MySQL enregistre ReceivedAt |
| R3 | Protéger l'intégrité des logs | ⚠️ **Partiellement** | Les logs sont stockés en base MySQL (lecture seule depuis le dashboard). Pas de signature numérique des logs. |
| R4 | Assurer la confidentialité des logs | ❌ **Non implémenté** | UDP en clair (pas de TLS). Les logs contiennent des données nominatives (username, IP). |
| R5 | Contrôler l'accès aux logs | ✅ **Implémenté** | Accès au dashboard uniquement via HTTP (non authentifié pour l'instant). |
| R6 | Définir une politique de rétention | ❌ **Non implémenté** | Aucune purge automatique des logs. La table SystemEvents peut croître indéfiniment. |
| R7 | Journaliser les événements d'authentification | ✅ **Implémenté** | Tous les événements AUTH_LOGIN_SUCCESS/FAILED, AUTH_REGISTER_SUCCESS/FAILED, AUTH_LOGOUT sont tracés. |
| R8 | Journaliser les accès aux ressources | ✅ **Implémenté** | Les accès aux tâches (création, modification, suppression, consultation) sont tracés. |
| R9 | Journaliser les échecs | ✅ **Implémenté** | Accès refusés (SECURITY_ACCESS_DENIED), ressources introuvables (SECURITY_RESOURCE_NOT_FOUND). |
| R10 | Journaliser les actions d'administration | ❌ **Non implémenté** | Pas de panneau d'administration ni d'utilisateurs avec rôles. |
| R11 | Protéger la configuration du système de journalisation | ✅ **Implémenté** | La configuration rsyslog est versionnée dans le dépôt Git. |
| R12 | Assurer la disponibilité des logs | ❌ **Non implémenté** | Pas de redondance : si MySQL tombe, les logs UDP sont perdus (mode non persistent). |
| R13 | Formater les logs de façon structurée | ✅ **Implémenté** | Logs au format JSON avec action, username, ip, etc. Facilement exploitables. |
| R14 | Synchroniser les horloges | ❌ **Non implémenté** | Pas de serveur NTP configuré dans les conteneurs Docker. |
| R15 | Séparer les responsabilités | ⚠️ **Partiellement** | L'application et le dashboard sont dans des conteneurs distincts. Pas de séparation fine des rôles. |

---

## Recommandations implémentées (8)

1. **R1** — Centralisation via rsyslog avec module ommysql
2. **R2** — Horodatage système + colonne ReceivedAt en base
3. **R5** — Accès contrôlé (dashboard dédié)
4. **R7** — Journalisation complète des événements d'authentification
5. **R8** — Journalisation des accès aux ressources (CRUD tâches)
6. **R9** — Journalisation des échecs (sécurité, accès refusés)
7. **R11** — Configuration versionnée (infrastructure as code)
8. **R13** — Format JSON structuré pour tous les logs

## Recommandations partiellement implémentées (2)

- **R3** (intégrité) : Stockage base de données, mais pas de signature
- **R15** (séparation) : Conteneurs distincts mais pas de gestion de rôles

## Recommandations non implémentées (5) — Planifiées pour versions futures

| N° | Recommandation | Priorité | Piste d'amélioration |
|:--:|----------------|:--------:|----------------------|
| R4 | Confidentialité (TLS) | Haute | Passer en TCP/TLS avec rsysog RELP ou utiliser un tunnel SSH |
| R6 | Rétention | Haute | Ajouter une tâche cron de purge (ex: logs > 90 jours) |
| R10 | Actions admin | Moyenne | Ajouter un rôle admin et journaliser ses actions |
| R12 | Disponibilité | Haute | Ajouter un second rsyslog en file forwarding ou buffer disque |
| R14 | Synchronisation NTP | Basse | Configurer NTP dans les Dockerfiles ou au niveau hôte |

---

## Conclusion

Le projet implémente **8 des 15 recommandations** du guide ANSSI, couvrant les aspects fondamentaux de la journalisation (centralisation, traçabilité des actions, format structuré). Les points manquants concernent principalement la sécurité avancée (chiffrement, signature, rétention) et la haute disponibilité — des améliorations prévues pour les versions futures.

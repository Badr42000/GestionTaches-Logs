# Analyse des recommandations ANSSI — Journalisation

Référence : **Guide ANSSI-PA-012 v2.0 « Recommandations de sécurité relatives à l'architecture d'un système de journalisation »** (janvier 2022)

> **Note :** Les numéros de recommandations (R1 à R31) ci-dessous correspondent aux références exactes du guide source. Le guide est structuré en chapitres couvrant les prérequis (chap. 2), l'architecture (chap. 3) et la configuration des générateurs (chap. 4).

---

## Recommandations analysées

| Guide | Recommandation | Prise en compte | Détail |
|:----:|----------------|:---------------:|--------|
| §2.1 / R1 | Définir une fonction de journalisation | ✅ **Implémenté** | LoggerInterface + SyslogLogger dédiés, format JSON structuré, 15 types d'événements |
| §2.2 / R2 | Horodatage fiable des évènements | ✅ **Implémenté** | rsyslog ajoute un timestamp système, MySQL enregistre ReceivedAt |
| §2.3 / R3 | Synchroniser les horloges (NTP) | ❌ **Non implémenté** | Pas de serveur NTP configuré dans les conteneurs Docker |
| §2.4 / R4 | Configurer les politiques de journalisation | ✅ **Implémenté** | Configuration rsyslog versionnée (imudp, ommysql), rotation et rétention à définir |
| §2.5 / R5 | Dimensionner l'espace de stockage | ❌ **Non implémenté** | Aucune estimation ni limitation de la taille de la table SystemEvents |
| §3.1 / R6-R7 | Collecter et centraliser les journaux | ✅ **Implémenté** | rsyslog centralise les logs de GestionDeTâches via UDP, avec module ommysql |
| §3.2 / R8-R9 | Sécuriser les transmissions | ❌ **Non implémenté** | UDP en clair (pas de TLS). Les logs contiennent des données nominatives (username, IP) |
| §3.3 / R10 | Stocker les journaux de façon sécurisée | ⚠️ **Partiellement** | Logs en base MySQL (lecture seule depuis le dashboard). Pas de signature numérique |
| §3.4 / R11 | Protéger l'intégrité des journaux | ⚠️ **Partiellement** | Stockage en base, mais pas de checksum ni de WORM |
| §3.5 / R12 | Contrôler l'accès aux journaux | ⚠️ **Partiellement** | Dashboard dédié (port 8080) mais accès non authentifié |
| §3.6 / R13 | Assurer la disponibilité | ❌ **Non implémenté** | Pas de redondance : si MySQL tombe, les logs UDP sont perdus (mode non persistant) |
| §3.7 / R14 | Définir une politique de rétention | ❌ **Non implémenté** | Aucune purge automatique. La table SystemEvents peut croître indéfiniment |
| §4.1 / R15 | Journaliser les événements d'authentification | ✅ **Implémenté** | AUTH_LOGIN_SUCCESS/FAILED, AUTH_REGISTER_SUCCESS/FAILED, AUTH_LOGOUT tracés |
| §4.2 / R16 | Journaliser la gestion des comptes | ✅ **Implémenté** | Inscriptions et échecs tracés avec raison |
| §4.3 / R17 | Journaliser les accès aux ressources | ✅ **Implémenté** | CRUD tasks tracé (TASK_CREATED, UPDATED, DELETED, VIEWED, LISTED) |
| §4.4 / R18 | Journaliser les échecs | ✅ **Implémenté** | SECURITY_ACCESS_DENIED, SECURITY_RESOURCE_NOT_FOUND |
| §4.5 / R19 | Journaliser les actions d'administration | ❌ **Non implémenté** | Pas de panneau d'administration ni de rôles |
| §4.6 / R20 | Formater les journaux de façon structurée | ✅ **Implémenté** | Logs au format JSON avec action, username, ip, etc. |
| §4.7 / R21 | Protéger la configuration du système de journalisation | ✅ **Implémenté** | Configuration rsyslog versionnée dans le dépôt Git |
| §4.8 / R22 | Séparer les responsabilités | ⚠️ **Partiellement** | App et dashboard dans des conteneurs distincts. Pas de séparation fine des rôles |

---

## Recommandations implémentées (10)

1. **R1** (§2.1) — Fonction de journalisation définie via LoggerInterface
2. **R2** (§2.2) — Horodatage système + colonne ReceivedAt en base
3. **R4** (§2.4) — Configuration versionnée du système de journalisation
4. **R6-R7** (§3.1) — Collecte et centralisation via rsyslog + ommysql
5. **R15** (§4.1) — Journalisation complète des événements d'authentification
6. **R16** (§4.2) — Journalisation de la gestion des comptes
7. **R17** (§4.3) — Journalisation des accès aux ressources (CRUD tâches)
8. **R18** (§4.4) — Journalisation des échecs
9. **R20** (§4.6) — Format JSON structuré pour tous les logs
10. **R21** (§4.7) — Configuration versionnée (infrastructure as code)

## Recommandations partiellement implémentées (3)

- **R10** (§3.3) — Stockage sécurisé : base dédiée mais pas de chiffrement
- **R11** (§3.4) — Intégrité : stockage base mais pas de signature/checksum
- **R12** (§3.5) — Contrôle d'accès : dashboard dédié mais non authentifié
- **R22** (§4.8) — Séparation : conteneurs distincts mais pas de rôles

## Recommandations non implémentées (5) — Planifiées

| Guide | Recommandation | Priorité | Piste d'amélioration |
|:----:|----------------|:--------:|----------------------|
| §2.3 / R3 | Synchronisation NTP | Basse | Configurer NTP dans les Dockerfiles ou au niveau hôte |
| §2.5 / R5 | Dimensionnement stockage | Moyenne | Estimer et limiter la taille de SystemEvents |
| §3.2 / R8-R9 | Confidentialité (TLS) | Haute | Passer en TCP/TLS avec rsyslog RELP ou tunnel SSH |
| §3.6 / R13 | Disponibilité | Haute | Ajouter un second rsyslog en file forwarding ou buffer disque |
| §3.7 / R14 | Rétention | Haute | Ajouter une tâche cron de purge (logs > 90 jours) |
| §4.5 / R19 | Actions admin | Moyenne | Ajouter un rôle admin et journaliser ses actions |

---

## Conclusion

Le projet implémente **10 des recommandations** du guide ANSSI-PA-012 v2.0, couvrant les aspects fondamentaux de la journalisation (centralisation, traçabilité des actions, format structuré). Les points manquants concernent la sécurité avancée (chiffrement, signature, rétention) et la haute disponibilité — des améliorations prévues pour les versions futures.

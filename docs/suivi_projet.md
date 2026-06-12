# Suivi de projet — GestionTaches-Logs

## Indicateurs d'avancement

### Prévu vs Réalisé

| Phase | Jours prévus | Jours réels | Écart | Commentaire |
|:-----:|:------------:|:-----------:|:-----:|-------------|
| Phase 1 — Infrastructure Docker | 1 j (4h) | 1 j (9 juin, ~4h) | — | Réalisé comme prévu |
| Phase 2 — App GestionDeTâches | 1 j (8h) | 1 j (9 juin, ~6h) | -2h | Fonctionnalités de base livrées le jour 1 |
| Phase 3 — Réseau & Ports | 1 j (1h) | 1 j (9 juin, ~1h) | — | Intégré à la phase 1 |
| Phase 4 — Dashboard & documentation | 3 j (17h) | 2 j (10-11 juin, ~15h) | -2h | Dashboard, refacto MVC, PHPStan, tests, docs |

**Temps total réalisé : ~26h** (prévu 30h) sur **3 jours** (9-11 juin 2026).

### Jalons

| Date | Jalon | Statut | Livrable |
|:----:|-------|:------:|----------|
| 2026-06-09 | Infrastructure Docker opérationnelle | ✅ Atteint | docker-compose.yml, Dockerfiles, init.sql, rsyslog.conf |
| 2026-06-09 | Application GestionDeTâches fonctionnelle | ✅ Atteint | Auth (login/register/logout), CRUD tâches, templates |
| 2026-06-09 | Dashboard v1 — visualisation logs | ✅ Atteint | Dashboard avec logs et statistiques |
| 2026-06-10 | Refacto MVC + mutualisation Database | ✅ Atteint | Namespaces PSR-4, BaseController, tests unitaires |
| 2026-06-10 | Documentation projet | ✅ Atteint | docs/projet_presentation.md, README enrichi, analyse ANSSI |
| 2026-06-10 | PHPStan niveau max, 0 erreurs | ✅ Atteint | phpstan.neon, corrections de type |
| 2026-06-10 | Tests unitaires (PHPUnit, mocks PDO) | ✅ Atteint | 3 fichiers, 18 tests |
| 2026-06-11 | Finalisation documentation + échanges IA | ✅ Atteint | conversations IA, registre risques, suivi projet |
| 2026-06-11 | Évaluation finale | ✅ Atteint | docs/evaluation_prof.md |

### Courbe d'avancement

```
Charge par jour (en commits) :

09 juin  ████████████████████  15 commits  (infra + fonctionnalités de base)
10 juin  ████████████████████████████  20 commits  (refacto + documentation)
11 juin  ████████████████  12 commits  (finalisation)
         ──────────────────
         Total : 47 commits (hors merges : 45)
```

### Suivi de la charge par lot

| Lot | Tâches | Charge prévue | Charge réelle | Écart |
|-----|--------|:-------------:|:-------------:|:-----:|
| Infrastructure Docker | 5 tâches | 6,5h | 5h | -1,5h |
| Application GestionDeTâches | 6 tâches | 8,5h | 7h | -1,5h |
| Dashboard | 2 tâches | 5,5h | 5h | -0,5h |
| Réseau & Ports | 2 tâches | 1h | 1h | — |
| Documentation | 5 tâches | 8h | 6h | -2h |
| Tests & Qualité | 3 tâches | 3,5h | 2h | -1,5h |
| **Total** | **23 tâches** | **33h** | **26h** | **-7h** |

### Analyse des écarts

- **Planning initial surestimé** : certaines tâches (infra Docker, réseau) étaient plus rapides que prévu grâce à l'expérience de l'outillage
- **Dashboard réalisé en 1 jour au lieu de 2** : la mutualisation de Database via le namespace Shared a simplifié l'intégration
- **Documentation concentrée sur les jours 2-3** : rédigée en parallèle du développement, pas après
- **Pas de phase de rattrapage nécessaire** : le planning resserré sur 3 jours (au lieu de 5) a été tenu

---

## Rétrospective

### Points positifs
- Infrastructure Docker opérationnelle dès le premier jour
- Mutualisation réussie du code (Database, namespace Shared)
- Couverture outillage qualité : PHPStan niveau max, PHPUnit, Makefile
- Documentation complète livrée dans les temps

### Points d'amélioration
- Tests de validation sans artefacts de preuve (captures, logs bruts)
- Performances annoncées mais jamais mesurées formellement
- Registre des risques créé tardivement (dernier jour)
- Pas d'indicateurs de suivi alimentés pendant le projet (ce document a été créé a posteriori)

---

_Document créé le 11 juin 2026, mis à jour le 12 juin 2026._

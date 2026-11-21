---
title: "Setting — Indice canonico docs"
status: canonical
module: Setting
updated: 2026-10-20
---

> Indice canonico. Indici storici alternativi: `00-index.md` (759 byte), `index.md` (99 byte), `README.md`. Nessun file storico cancellato.

> Convenzione: [Docs Organization — Modules & Themes](../../Xot/docs/conventions/docs-organization.md)

# Setting — Indice docs

Modulo Setting (configurazione, agent methodologies, customizzazione sito). Gestisce configuration settings, agent behaviour e custom options della applicazione.

## Panoramica
- [README.md](./README.md) — presentazione del modulo
- [configuration.md](./configuration.md) — configurazione settings
- [ai-methodologies.md](./ai-methodologies.md) — metodologie AI adottate
- [architecture-rules.md](./architecture-rules.md) — regole architpected

## Architettura
- [bmad/](./bmad/) — dossier BMAD (livewire-widget-*, livewire-widget-decision-log, setup-guide)
- [development/](./development/) — solo se presente nel modulo
- [decision-log.md](./decision-log.md) — decisioni
- [decision-log.md](./decision-log.md) — decisioni

## PHPStan / Qualità
- [coverage.md](./coverage.md) — report copertura
- [phpstan-report.md](./phpstan-report.md) — report PHPStan
- [phpstan-roadmap.md](./phpstan-roadmap.md) — roadmap PHPStan
- [no-phpstan-probe-policy.md](./no-phpstan-probe-policy.md) — policy sezione probe

## BMAD / Stories
- [bmad/](./bmad/) — dossier BMAD
- [stories/](./stories/) — storie feature (setting-quality-gate-.story.md, git-remote-missing, continuation)
- [stories/debug-calls-removal-setting.story.md](./stories/debug-calls-removal-setting.story.md) — chiamata di debug (dddx) in `DatabaseBackupTableAction`, ora errore esplicito; azione non registrata, decisioni aperte

## Struttura (sottocartelle)
- `bmad/` — dossier BMAD
- `stories/` — storie feature
- `concepts/` — concetti archittechurali
- `wiki/` — wiki (in ingresso non abbiamo però un wiki; la cartella esiste ma si presume "")
- `schema/`, `logs/`, `outputs/`, `raw/`, `screenshots/` — utility/asset

## Duplicati storici mantenuti
- `00-index.md` (esistente, 759 byte, 2026-09-24) — parte riferito; non cancellato.
- `index.md` (esistente, 99 byte, 2026-08-24) — parte riferito; non cancellato.
- `CHANGELOG.md` (286 byte) — mantenuto

---
*Aggiornato: 2026-10-20 — conforme a `docs-organization.md`; `00-index.md` e `index.md` mantenuti; nessun file cancellato.*

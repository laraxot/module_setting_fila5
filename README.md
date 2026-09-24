<<<<<<< HEAD
# Setting Module

## Overview

`Setting` is a **minimal nwidart/laravel-modules scaffold** for future system-configuration
management in Quaeris Fila5. As of 2026-07-20 it has no persistence layer: no Eloquent
models, no migrations, no Actions, no Filament resources. It is a plain resource
controller wired to Blade views, registered via the standard module boot lifecycle
(config, views, translations, migrations-from-empty-directory).

## What Actually Exists

- `app/Http/Controllers/SettingController.php` — standard 7-method resource controller.
  `store`, `update`, `destroy` are no-ops that return `204 No Content`; `index`/`create`/
  `show`/`edit` render Blade stubs (`resources/views/index.blade.php` etc.) that print
  `config('setting.name')`. There is no model, no request validation, no persistence.
- `app/Providers/SettingServiceProvider.php` — boots translations, config, views,
  factories, and migrations (the `database/migrations/` directory is currently empty).
- `config/config.php` — static array (`name`, `version`, `description`, `author`,
  `enabled`, `priority`). This is module metadata, not a settings-storage config.
- `database/seeders/SettingDatabaseSeeder.php` — empty `run()`, calls nothing.
- `routes/web.php` — `Route::resource('settings', SettingController::class)` behind
  `auth`, `verified`.
- `routes/api.php` — `Route::apiResource('settings', SettingController::class)` behind
  `auth:sanctum`, prefixed `v1`.
- `tests/Feature/` and `tests/Unit/` contain only `.gitkeep` — no tests exist yet.

## What Does Not Exist (previously documented here, not true)

- No `/settings/dashboard`, `/settings/general`, `/settings/modules`,
  `/settings/users`, `/settings/system` routes — only the standard resource
  routes above exist.
- No Livewire components — views are plain Blade (`x-setting::layouts.master`).
- No real RESTful settings API beyond the stub `apiResource` (no read/write logic).
- No user-preferences or module-specific settings storage.

## Next Steps (if this module is built out)

Real functionality would require an Eloquent model + migration for key/value
storage (or adopting a settings package such as `spatie/laravel-settings`),
QueueableAction classes under `app/Actions/` for get/set/cache operations, and
Filament resources under `app/Filament/` for admin UI — none of which exist today.

## Related Docs

- [docs/00-index.md](docs/00-index.md) — documentation index
- [Xot Module](../Xot/docs/00-index.md) — Core XotBase conventions
=======
# Setting: il modulo che trasforma complessita in vantaggio operativo

Settings and configuration management module for the Laraxot ecosystem: application preferences, feature toggles, and system parameters.

## Perche guardarlo adesso

- Riduce attrito operativo con convenzioni Laraxot gia pronte.
- Porta documentazione, release e changelog nello stesso flusso verificabile.
- Aiuta team e agenti AI a capire subito scopo, confini e prossime mosse.
- E pensato per crescere: semantic versioning, auto release e changelog automatico sono gia configurati.

## Cosa promette

Questo modulo non e solo codice: e una vetrina operativa. Mostra dove intervenire, cosa leggere, come rilasciare e come mantenere alta la confidenza tecnica.

## Release automation

- Workflow: [Semantic Release](./.github/workflows/semantic-release.yml)
- Config: [.releaserc.json](./.releaserc.json)
- Changelog: [CHANGELOG.md](./CHANGELOG.md)


## Documentazione tecnica

- [Indice docs](./docs/README.md) — mappa knowledge base locale (wiki, audit, regole)

## Documentazione essenziale

- [Second brain locale](./docs/wiki/index.md)
- [Audit ridondanza](./docs/code-redundancy-audit.md)
- [Protocollo confidenza](./docs/agent-confidence-protocol.md)
- [Disciplina agenti](./docs/agent-edit-discipline.md)
- [Changelog](./docs/CHANGELOG.md)
- [Algolia Docsearch](./docs/algolia-docsearch.md)
- [Analysis](./docs/analysis.md)
- [Api Integration](./docs/api-integration.md)
- [Architecture Rules](./docs/architecture-rules.md)
- [Best Practices](./docs/best-practices.md)

## Filosofia

Scopo prima del codice. DRY prima dell'orgoglio. KISS prima dell'astrazione. La release automatica non sostituisce il giudizio: lo rende tracciabile.
>>>>>>> laraxot/dev

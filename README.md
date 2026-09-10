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

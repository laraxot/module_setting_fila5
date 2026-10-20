---
title: "Blueprint Filament e basi Xot per il modulo Setting"
type: story
status: ready-for-dev
priority: P1
created: 2026-10-20
updated: 2026-10-20
module: Setting
epic: ../../../Xot/docs/bmad/epic-code-quality-ratchet.md
depends_on: ["./setting-backup-database-senza-shell.story.md (decide la sorte di DatabaseBackupTableAction e della pagina BackupMysql)", "verdetto Pest P0 dell'utente (solo per la fase 3)", "guardia DB dentro (solo per la fase 3)"]
related: [../bmad/livewire-inventory.md, ./debug-calls-removal-setting.story.md, ../../../User/docs/bmad/filament-blueprint.md, ../../../Xot/docs/bmad/filament-blueprint.md, ../../../Xot/docs/stories/code-quality-filament-label-audit.story.md]
tags: [bmad, filament, blueprint, xotbase, lang, enum]
---

# Blueprint Filament e basi Xot per il modulo Setting

Fase BMAD: Story pronta per lo sviluppo (analisi, nessun codice toccato).

## Richiesta

Come sviluppatore del modulo voglio un contratto scritto per la parte Filament di Setting e pagine che estendono solo basi `XotBase*`,
cosi' che la regola "mai classi Filament dirette, nessuna stringa UI nel codice" sia verificabile e non solo dichiarata.
Oggi 5 classi saltano la base Xot, 13 testi di opzione sono scritti nel codice e il blueprint (`docs/bmad/filament-blueprint.md`) non esiste.

## Scopo

Il modulo gestisce la schermata "Database Connection" (elenco, vista, creazione e modifica delle connessioni di `config('database.connections')`
tramite il modello Sushi `Models\DatabaseConnection`), la pagina `BackupMysql` e una `Dashboard`. Serve all'amministratore per vedere le
connessioni e provarle. Le basi Xot danno a tutte le pagine gli stessi comportamenti (traduzioni, permessi, azioni di testata): saltarle
significa che questo modulo si comporta diversamente dagli altri (72 pagine Create, 77 Edit, 43 View nel repo le usano).

## Stato misurato

Misure del 2026-10-20 13:47 UTC, cartella `laravel/`.
- File: `/usr/bin/find Modules/Setting/app/Filament -name '*.php' | wc -l` = 12 (con `-type f`: 13, c'e' un `.gitkeep`). Test: `/usr/bin/find Modules/Setting/tests -name '*.php' | wc -l` = 3 (`Pest.php`, `TestCase.php`, `Unit/DatabaseConnectionTest.php`); con casi: `rg -c -P -e '^(it|test)\(|function test|#\[Test\]' Modules/Setting/tests` = 1 file, 4 metodi, mai eseguiti oggi (Pest fermo).
- Blueprint: `/usr/bin/find Modules/Setting/docs -iname 'filament-blueprint*'` = nessun file. Controprova: lo stesso comando su `Modules/User/docs` trova `bmad/filament-blueprint.md`.
- `extends` diretti di classi Filament: sono 5, non 3. `rg -n -P "^\s*(abstract\s+|final\s+)*class\s+\w+\s+extends\s+(?!XotBase)\w+" --glob '*.php' Modules/Setting/app/Filament` e `git -C Modules/Setting grep -n -P "extends\s+(CreateRecord|ViewRecord|EditRecord|ManageRecords|ListRecords|Action|Page|Widget|Resource)\b" -- app/Filament` danno le stesse righe: `CreateDatabaseConnection.php:11` (CreateRecord), `ViewDatabaseConnection.php:14` (ViewRecord), `EditDatabaseConnection.php:13` (EditRecord), `ManageDatabaseConnections.php:11` (ManageRecords), `Actions/Table/DatabaseBackupTableAction.php:11` (Action). Le altre 7 classi estendono gia' `XotBase*` (Resource, ListRecords, Page, Dashboard, Form, Table, Infolist).
- Basi disponibili in `Modules/Xot/app/Filament/Resources/Pages/`: `XotBaseCreateRecord` (usata da 72 file in `Modules/*/app`), `XotBaseEditRecord` (77), `XotBaseViewRecord` (43), `XotBaseManageRecords` (2); per l'azione `Filament/Actions/XotBaseAction.php` (26 file; `XotBaseTableAction` ha 0 usi). Misura: `rg -l -P -e "extends XotBaseViewRecord\b" --glob '*.php' Modules/*/app | wc -l`.
- Stringhe UI: `->label('...')` = 0 file in Setting. Controprova che il pattern vive: `rg -l -P -e "->label\(\s*['\"]" Modules/Quaeris/app | wc -l` = 25. Con altri metodi (`->placeholder|helperText|heading|description|tooltip|title(` con letterale) = 0. Ma i testi ci sono, nelle opzioni: `rg -c -P "=>\s*'(MySQL|PostgreSQL|SQLite|SQL Server|Active|Inactive|Testing)'" Modules/Setting/app/Filament` = 13 valori (Form 6, righe 28-31 e 63-64; Table 7, righe 88-91 e 95-97), confermato da `git grep -P` = 13. Nelle viste: `dashboard.blade.php:6` ("Filament upgrade"), `backup-mysql.blade.php:9,10,19` ("name", "database", "Download").
- Azione `test`: tre copie della stessa closure, senza etichetta tradotta e senza notifica del risultato (`testConnection()` torna `bool` e nessuno lo guarda): `Tables/DatabaseConnectionsTable.php:108`, `Pages/ViewDatabaseConnection.php:23`, `Pages/EditDatabaseConnection.php:21`. Duplicati anche `getRedirectUrl()` (Create e Edit) e il blocco `if status === 'active' testConnection()` (`afterCreate`, `afterSave`).
- `ManageDatabaseConnections`: nessun riferimento (`rg -n -P -e "ManageDatabaseConnections" --glob '*.php' Modules/Setting/app Modules/Xot/app` e `git grep -w`: solo la definizione); `XotBaseResource::getPages()` (`Modules/Xot/app/Filament/Resources/XotBaseResource.php:295-324`) costruisce index, create, edit, view per convenzione `List*`/`Create*`/`Edit*`/`View*`, quindi `Manage*` non e' mai registrata.
- Lingua: la risorsa usa `lang/it/database_connection.php`, i cui `fields` hanno 7 chiavi (`name`, `guard_name`, `permissions`, `updated_at`, `first_name`, `last_name`, `select_all`): dei 14 campi del form solo `name` coincide (residuo di un'altra risorsa). `lang/it/database_connections.php` (plurale) ha 14 chiavi `fields.<campo>.label` ma e' un file diverso. Presenti anche `lang/lang/it/database_connection.php` (cartella annidata, differisce) e `lang/it/.php` (file senza nome, tracciato da git). Non verificato a runtime quale chiave risolva `GetTransKeyAction`.
- Livewire: `/usr/bin/find Modules/Setting -path '*/Http/Livewire/*.php' | wc -l` = 0; inventario gia' approvato in `docs/bmad/livewire-inventory.md`, nessun piano Livewire-widget da aprire.

## Lavoro

Fase 1, blueprint (solo documento). Scrivere `laravel/Modules/Setting/docs/bmad/filament-blueprint.md` sul modello di `Modules/User/docs/bmad/filament-blueprint.md` (46 righe): scopo, regole non negoziabili (solo basi Xot, nessuna stringa UI, logica nelle Actions, nessun segreto mostrato), elenco di Resource e Page (`DatabaseConnectionResource`, `BackupMysql`, `Dashboard`), schemi `Form`/`Table`/`Infolist`, policy (`DatabaseConnectionPolicy` e il permesso della pagina backup della story collegata), chiavi lingua. Frontmatter `type: filament-blueprint`.

Fase 2, basi (un file alla volta, `php -l` dopo ciascuno, un solo agente PHP sul modulo):
1. `CreateDatabaseConnection` a `Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord`. Rischio basso: la base aggiunge solo `getDefaultFormData()` che torna `[]`.
2. `EditDatabaseConnection` a `XotBaseEditRecord`. La base ha gia' `'delete' => DeleteAction` in `getHeaderActions(): array<string, Action|ActionGroup>`: l'override deve partire da `parent::getHeaderActions()` e aggiungere `'test'` con chiave stringa (oggi ritorna una lista e duplicherebbe il Delete). La base usa `TransTrait` per `getNavigationLabel()`/`getNavigationIcon()`: controllare le chiavi lingua.
3. `ViewDatabaseConnection` a `XotBaseViewRecord` (usa `HasFiltersForm`; stessa regola chiavi stringa per gli header action).
4. `ManageDatabaseConnections`: decisione 1. Se si tiene: `XotBaseManageRecords`. Se si cancella: nessun altro file da toccare.
5. `DatabaseBackupTableAction`: la decide la story collegata; se resta, `XotBaseAction`.
6. Nuova `Filament/Actions/TestConnectionAction.php` (base `XotBaseAction`, etichetta da lang, `Notification` di successo o errore secondo il `bool`), usata da Table, View, Edit al posto delle tre closure.
7. Enum `Enums/DatabaseDriver.php` e `Enums/DatabaseConnectionStatus.php` (backed enum, `HasLabel` con etichetta da lang) al posto dei 13 testi; chiavi in `lang/it/database_connection.php` con la forma `fields.<campo>.label`, poi pulizia dei residui (`guard_name`, `first_name`...). Vista Dashboard: testo in lang (la vista `backup-mysql` e' nella story collegata, passo 6).
8. Dal codice, non provato in browser: il form precompila `password` dal config (`Models\DatabaseConnection::getRows()` la espone) e la manda al client. Il blueprint deve vietarlo (password fuori dal form di edit o `->dehydrated(false)` e non precompilata).

Fase 3, test. Dopo guardia e verdetto Pest: `Modules/Setting/tests/Architecture/FilamentBasesTest.php` (non lega `Setting\Tests\TestCase`, che ha `DatabaseTransactions`) che controlla per reflection che ogni classe sotto `Modules\Setting\Filament` abbia un antenato `Modules\Xot\Filament\*`; test degli enum senza app; per le pagine `livewire(...)` solo dopo conferma sui DB `*_test`.

## Acceptance criteria

- [ ] `/usr/bin/find Modules/Setting/docs/bmad -name filament-blueprint.md | wc -l` = 1.
- [ ] `rg -n -P -e "extends (CreateRecord|ViewRecord|EditRecord|ManageRecords|ListRecords|Action)\b" --glob '*.php' Modules/Setting/app/Filament` = nessuna riga (oggi 5).
- [ ] `rg -n -P "^use Filament\\\\Resources\\\\Pages\\\\" --glob '*.php' Modules/Setting/app/Filament` = nessuna riga (oggi 4).
- [ ] `rg -c -P "=>\s*'(MySQL|PostgreSQL|SQLite|SQL Server|Active|Inactive|Testing)'" Modules/Setting/app/Filament` = nessun file (oggi 13 valori in 2 file).
- [ ] `rg -n -P -e "Action::make\('test'\)" --glob '*.php' Modules/Setting/app/Filament` = 1 riga (dentro `TestConnectionAction`; oggi 3).
- [ ] Nessuna chiave usata e' priva di traduzione: ogni `trans(...)`/`->label` del modulo compare in `lang/it/`.
- [ ] PHPStan gated su `Modules/Setting/app/Filament` e `Modules/Setting/app/Enums`: exit 0, senza ignore.
- [ ] Fase 3: test architetturale e test enum verdi senza aprire connessioni al database.

## Verifica

1. Ogni AC sopra deve fallire prima del lavoro (le cifre "oggi" sono la controprova).
2. Dopo la fase 2: i due `rg` sugli `extends` e sugli `use Filament\Resources\Pages` tornano exit 1 (nessun output); la controprova e' lo stesso comando prima del lavoro, che stampa 5 righe (`rg -c ... | wc -l` = 5 file).
3. `php -l` su ogni file toccato, poi il gate PHPStan del blocco Vincoli su `Modules/Setting/app/Filament`.
4. In browser, solo con l'utente: aprire elenco, vista, modifica, e premere "test" su una connessione; l'esito deve comparire come notifica.

## Vincoli

- PHPStan: unica config `laravel/phpstan.neon`, la modifica solo l'utente; mai altri `.neon`, mai `-c`/`--level`, nessun baseline/ignore. Ogni invocazione passa dal gate condiviso: `cd laravel && flock -w 1500 /tmp/claude-1000/-mnt-nas07-var-www--bases-base-quaeris-fila5/aeabb365-8395-48f4-b7d4-9fbb17ad85d3/scratchpad/phpstan-gate.lock nice -n 10 ./vendor/bin/phpstan analyse <path...> --memory-limit=2G --no-progress --error-format=raw` (max ~8 file o un modulo; `analyse Modules` intero solo dopo il "run completo finito" dell'altra sessione).
- Pest e PHPUnit FERMI: il 2026-10-20 `RefreshDatabase` ha fatto `migrate:fresh` su `quaeris_data_test`. Si riprende solo quando la guardia DB e' dentro (story `laravel/Modules/Xot/docs/stories/test-database-guard.story.md`) e l'utente ha confermato che i DB `*_test` sono usa-e-getta. Fino ad allora la verifica e' PHPStan + `php -l` + lettura.
- Concorrenza: altre sessioni scrivono nello stesso repo. Prima di scrivere un file esistente: `bashscripts/lock/check.sh <f> && [ $(( $(date +%s) - $(stat -c %Y <f>) )) -gt 120 ] && bashscripts/lock/lock.sh <f>`; un solo agente PHP per modulo; niente commit, reset, checkout, stash.
- zsh non divide `$VAR` in argomenti: usa array (`A=(x y); cmd $A`) o `xargs -a`. Un pattern `rg` che inizia con `-` richiede `-e`. Un `0` va sempre controprovato (rg -P piu' un secondo metodo).
- Docs: artefatti BMAD solo in `laravel/Modules/<M>/docs/`; nomi senza date; nessun Service (`app/Services` vietata, logica in Spatie QueueableAction con `execute()`); Filament: solo basi `XotBase*`, logica nelle Actions, nessuna stringa UI hardcoded (chiavi in `lang/`).
- Specifici: non modificare `Modules/Xot` (le basi si usano, non si cambiano); nessuna nuova tabella o colonna; `phpstan.neon` intoccabile.

## Decisioni aperte

1. Cancellare `ManageDatabaseConnections` (mai registrata). Raccomandazione: cancellare, perche' `ListDatabaseConnections` copre l'indice e il codice morto confonde.
2. Create, Edit e Delete su un modello Sushi che legge da `config('database.connections')` e non ha tabella (`Modules/Setting/database/migrations` e' vuota: `ls` senza output; `rg -n -i database_connection Modules/Setting/database` = exit 1): tenerli o ridurre la risorsa a elenco, vista e test? Raccomandazione: sola lettura piu' "test", da confermare in browser prima di rimuovere le pagine.
3. Proprieta' degli enum: restano in `Modules/Setting/app/Enums` (raccomandato) o vanno in Xot.

## Come ripartire

1. `cd laravel && sed -n 1,46p Modules/User/docs/bmad/filament-blueprint.md && ls Modules/Xot/app/Filament/Resources/Pages`: modello del blueprint e basi disponibili.
2. Scrivere `Modules/Setting/docs/bmad/filament-blueprint.md` (fase 1) e verificare con `/usr/bin/find Modules/Setting/docs/bmad -name filament-blueprint.md`.
3. Poi fase 2 passo 1-3, `php -l` e gate PHPStan su `Modules/Setting/app/Filament`; annotare qui cifre con data e ora UTC.

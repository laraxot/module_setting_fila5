---
title: "Backup del database senza shell: password fuori dalla riga di comando"
type: story
status: ready-for-dev
priority: P1
created: 2026-10-20
updated: 2026-10-20
module: Setting
epic: ../../../Xot/docs/bmad/epic-code-quality-ratchet.md
depends_on: ["guardia DB dentro: Modules/Xot/tests/Support/TestDatabaseGuard.php (solo per la fase test)", "conferma dell'utente che i DB *_test sono usa-e-getta (solo per la fase test)", "run completo PHPStan finito (solo per analyse Modules intero)"]
related: [./debug-calls-removal-setting.story.md, ../../../Xot/docs/stories/test-database-guard.story.md, ../../../DbForge/docs/stories/debug-calls-removal-dbforge.story.md]
tags: [bmad, sicurezza, backup, mysqldump, process, autorizzazione]
---

# Backup del database senza shell: password fuori dalla riga di comando

Fase BMAD: Story pronta per lo sviluppo (analisi fatta, nessun codice toccato).

## Richiesta

Come amministratore voglio scaricare il dump di una connessione MySQL dal pannello cosi' che la password non finisca in `ps`,
nei log e in una stringa di shell, e che solo chi ha il permesso possa farlo.
Oggi `Actions\DB\DownloadAction` compone `mysqldump ... | gzip > file` come stringa e la passa alla shell: un valore di config con
spazi o metacaratteri rompe il comando o ne cambia il significato, e la password e' visibile a ogni utente della macchina.

## Scopo

`Pages\BackupMysql` elenca le connessioni `driver === 'mysql'` di `config('database.connections')` e, per ognuna, un bottone che
chiama `download($nome)`. L'action fa il dump con `mysqldump`, lo comprime in `.gz` sul disco `cache` e lo restituisce come
download. Serve a chi amministra l'installazione e vuole una copia dei dati prima di un intervento. Il dump contiene tutti i dati
delle connessioni, quindi e' l'operazione piu' sensibile del modulo.

## Stato misurato

Misure del 2026-10-20 13:44 UTC, cartella `laravel/`.
- Riga di comando con password: `rg -n -P -e '--password=' --glob '*.php' Modules/*/app app` = 3 file: `Setting/app/Actions/DB/DownloadAction.php:42`, `Xot/app/Console/Commands/DatabaseBackUpCommand.php:51`, `DbForge/app/Console/Commands/DatabaseBackUpCommand.php:55`. Conferma con `git grep -n -e '--password=' -- 'Modules/*/app/*.php'`: stessi 3.
- Pipe verso gzip: `rg -n -P -e '\|\s*gzip' --glob '*.php' Modules/*/app app` = gli stessi 3 file (Xot:57, DbForge:55, Setting:42).
- `escapeshellarg` nel modulo: `rg -c -P -e 'escapeshellarg' --glob '*.php' Modules/Setting/app` = nessun file. Controprova che il pattern funziona: `rg -c -P -e '--password=|\|\s*gzip|escapeshellarg' --glob '*.php' Modules/Setting/app` = 1 file (DownloadAction).
- Chiamanti di `DownloadAction`: `rg -n -P "DownloadAction" --glob '*.php' Modules app` e `git grep -n -w DownloadAction -- '*.php'` danno gli stessi: definizione, `Pages/BackupMysql.php:7` e `:18`, un commento in `DatabaseBackupTableAction.php:26`. Un solo chiamante reale.
- `DatabaseBackupTableAction`: nessun chiamante (`rg -n -P "DatabaseBackupTableAction" --glob '*.php' Modules app` e `git grep -w`: solo la definizione; la chiave `database-backup` e' in `lang/it/database_connection.php:29`). Il `->action()` lancia `LogicException` (riga 27).
- Test del modulo che toccano backup: `rg -c -P "mysqldump|DownloadAction" Modules/Setting/tests` = nessun file (exit 1). Il pattern funziona: sul sorgente `DownloadAction.php` trova 1 file.
- Difetti letti nel codice, non eseguiti: nessun `--host`/`--port` (il dump punta al socket locale anche se la connessione e' remota); l'exit code non e' controllato e senza `pipefail` la pipeline torna quello di `gzip`, quindi un dump fallito restituisce un `.gz` valido e vuoto; il file resta sul disco dopo il download; `download(string $connectionName)` accetta qualunque stringa e il nome entra nel nome del file; il disco `cache` non e' in `config/filesystems.php`: lo definiscono 13 dei 14 `config/**/filesystems.php` per ambiente (`/usr/bin/find config -name filesystems.php`; `rg --files-without-match -P -e "^\s*'cache'\s*=>\s*\["` su quell'elenco restituisce solo `config/filesystems.php`), tutti con `visibility => public`, e in `config/localhost` e `config/local/quaeris` con `root => base_path('../cache')` (cartella oggi inesistente, ignorata da `.gitignore:14`). Se il disco manca nell'ambiente, `Storage::disk('cache')` lancia eccezione.
- Autorizzazione: `rg -n -P "canAccess" Modules/Setting/app` = nessun file. In Filament la `canAccess()` di default e' `true` (`vendor/filament/filament/src/Pages/Concerns/CanAuthorizeAccess.php:17-24`) e viene riletta a ogni richiesta Livewire (`hydrateCanAuthorizeAccess`). `XotBasePage::authorizeAccess()` (riga 254) non e' chiamato da nessuno: codice morto.
- Altro in `Pages/Dashboard.php:20`: `LaravelProcess::run('php artisan filament:upgrade')`, stringa costante, nessun input; da portare in forma array nello stesso passaggio.

## Lavoro

1. `Actions/DB/DumpDatabaseAction.php` (nuovo, `QueueableAction`, `execute(string $connection): string` ritorna il percorso del `.gz`). Nome connessione: ammesso solo se e' chiave di `config('database.connections')`, `driver` in `mysql|mariadb`, e `preg_match('/^[A-Za-z0-9_-]+$/')`. Utente, host, porta e database vengono dal config, mai dalla richiesta.
2. Stesso file: `Process::env(['MYSQL_PWD' => $password])->timeout(600)->run(['mysqldump', '--user='.$u, '--host='.$h, '--port='.$p, '--single-transaction', '--result-file='.$tmpSql, $db])`, poi `->throw()`. Nessuna shell, nessuna password in argv. Rischio: MariaDB e MySQL leggono `MYSQL_PWD`, ma MySQL lo dichiara deprecato; il client qui e' `mysqldump 11.8.6-MariaDB`. Alternativa se serve: file `--defaults-extra-file` con permessi 0600, cancellato in `finally`.
3. Compressione in PHP: `gzopen($path, 'wb6')` e lettura a blocchi da 1 MiB dello `.sql` (nessun `| gzip`), poi `unlink($tmpSql)`. Costo: spazio temporaneo doppio; vantaggio: nessun buffer in memoria del Process.
4. Percorso di output: disco privato (`storage_path('app/private/backup')`, root del disco `local`), mai `cache` (public, fuori da `storage`). Prima di scrivere: `realpath` della cartella deve iniziare con `storage_path()`. Cartella con `0o700`, file con `0o600`. Nome: `backup-<connessione>-<Y-m-d-His>.sql.gz`.
5. `Actions/DB/DownloadAction.php`: autorizza, chiama `DumpDatabaseAction`, torna `response()->download($path)->deleteFileAfterSend()`. Se l'exit code non e' 0 niente file e un errore leggibile (nessuna password nel messaggio).
6. `Filament/Pages/BackupMysql.php`: `public static function canAccess(): bool` con un permesso dedicato (modello: `Activity/app/Filament/Pages/LogViewer.php:58`, che delega a `AuthorizeLogAccessAction`). Nel metodo `download()` validare `$connectionName` contro l'elenco gia' calcolato in `getViewData()` prima di chiamare l'action. La vista `backup-mysql.blade.php` ha testi letterali (`name`, `database`, `Download`): spostarli in `lang/it/backup_mysql.php`.
7. `Filament/Actions/Table/DatabaseBackupTableAction.php`: decisione aperta 2. Se si cancella: togliere anche la chiave `database-backup` da `lang/it/database_connection.php:29-33` e correggere i due docs che la citano (`docs/bmad/livewire-inventory.md`, `docs/filament-v4-upgrade.md`).
8. `Pages/Dashboard.php:20`: `LaravelProcess::run(['php', 'artisan', 'filament:upgrade'])`.
9. Test (solo dopo la guardia DB): `tests/Unit/Actions/DumpDatabaseActionTest.php` con `Process::fake()` e `Process::assertRan(...)` che controlla array senza `--password`, `MYSQL_PWD` nell'ambiente, rifiuto di `../x` e di un driver non mysql. Non usare `Modules\Setting\Tests\TestCase` (ha `DatabaseTransactions` e `tests/Pest.php` lo lega a `Unit` e `Feature`): scegliere una base senza database.

## Acceptance criteria

- [ ] `rg -n -P -e '--password=' --glob '*.php' Modules/Setting/app` = nessuna riga.
- [ ] `rg -n -P -e '\|\s*gzip|sprintf\(.mysqldump' --glob '*.php' Modules/Setting/app` = nessuna riga.
- [ ] `rg -n -P -e "\['mysqldump'" --glob '*.php' Modules/Setting/app` = 1 riga (forma array).
- [ ] `rg -n -P -e 'MYSQL_PWD|defaults-extra-file' --glob '*.php' Modules/Setting/app` = almeno 1 riga.
- [ ] `rg -n -P -e 'function canAccess' Modules/Setting/app/Filament/Pages/BackupMysql.php` = 1 riga.
- [ ] `rg -n -P -e 'Storage::disk\(.cache.\)' Modules/Setting/app` = nessuna riga.
- [ ] PHPStan gated su `Modules/Setting/app/Actions/DB`, `Modules/Setting/app/Filament/Pages`: exit 0 senza ignore.
- [ ] Test con `Process::fake` verdi, senza aprire connessioni al database.
- [ ] Decisione 2 applicata: `DatabaseBackupTableAction` o cancellata (con la chiave lang) o con `action()` che chiama l'action autorizzata.

## Verifica

1. Prima del lavoro, ogni AC sopra deve fallire: `rg -n -P -e '--password=' --glob '*.php' Modules/Setting/app` stampa `DownloadAction.php:42`.
2. Dopo: stessa riga = nessun output (exit 1). Controprova che il pattern vive: `rg -n -P -e '--password=' --glob '*.php' Modules/Xot/app/Console` stampa ancora la riga 51 finche' i comandi sibling non sono migrati.
3. `php -l` su ogni file toccato, poi il gate PHPStan del blocco Vincoli.
4. Prova reale (solo dopo conferma dell'utente, su un DB usa-e-getta): lanciare l'action, `zcat <file> | head`, `ps -eo args | rg mysqldump` durante l'esecuzione non deve mostrare la password.

## Vincoli

- PHPStan: unica config `laravel/phpstan.neon`, la modifica solo l'utente; mai altri `.neon`, mai `-c`/`--level`, nessun baseline/ignore. Ogni invocazione passa dal gate condiviso: `cd laravel && flock -w 1500 /tmp/claude-1000/-mnt-nas07-var-www--bases-base-quaeris-fila5/aeabb365-8395-48f4-b7d4-9fbb17ad85d3/scratchpad/phpstan-gate.lock nice -n 10 ./vendor/bin/phpstan analyse <path...> --memory-limit=2G --no-progress --error-format=raw` (max ~8 file o un modulo; `analyse Modules` intero solo dopo il "run completo finito" dell'altra sessione).
- Pest e PHPUnit FERMI: il 2026-10-20 `RefreshDatabase` ha fatto `migrate:fresh` su `quaeris_data_test`. Si riprende solo quando la guardia DB e' dentro (story `laravel/Modules/Xot/docs/stories/test-database-guard.story.md`) e l'utente ha confermato che i DB `*_test` sono usa-e-getta. Fino ad allora la verifica e' PHPStan + `php -l` + lettura.
- Concorrenza: altre sessioni scrivono nello stesso repo. Prima di scrivere un file esistente: `bashscripts/lock/check.sh <f> && [ $(( $(date +%s) - $(stat -c %Y <f>) )) -gt 120 ] && bashscripts/lock/lock.sh <f>`; un solo agente PHP per modulo; niente commit, reset, checkout, stash.
- zsh non divide `$VAR` in argomenti: usa array (`A=(x y); cmd $A`) o `xargs -a`. Un pattern `rg` che inizia con `-` richiede `-e`. Un `0` va sempre controprovato (rg -P piu' un secondo metodo).
- Docs: artefatti BMAD solo in `laravel/Modules/<M>/docs/`; nomi senza date; nessun Service (`app/Services` vietata, logica in Spatie QueueableAction con `execute()`); Filament: solo basi `XotBase*`, logica nelle Actions, nessuna stringa UI hardcoded (chiavi in `lang/`).
- Specifici: nessun `mysqldump` mai lanciato contro un DB reale per provare; niente password in messaggi di errore, log o test; nessun nuovo permesso scritto nel database da questa story (solo il nome nel codice, il seeder lo decide l'utente).

## Decisioni aperte

1. Nome e assegnazione del permesso (proposta: `database_connection.backup`, solo ruolo super admin). Raccomandazione: usare la forma gia' presente in `DatabaseConnectionPolicy` (`hasPermissionTo('database_connection.<abilita>')`) e aggiungere il metodo `backup()`.
2. `DatabaseBackupTableAction`: cancellare o completare. Raccomandazione: cancellare, perche' la pagina `BackupMysql` copre il caso e una seconda uscita raddoppia la superficie di rischio.
3. Comandi sibling `Xot/.../DatabaseBackUpCommand.php` e `DbForge/.../DatabaseBackUpCommand.php`: stesso difetto, stessa firma `database:backup` registrata due volte. Raccomandazione: due story separate nei loro moduli che chiamano `DumpDatabaseAction` e tengono un solo comando.
4. Metodo password: `MYSQL_PWD` (semplice) o `--defaults-extra-file` (portabile). Raccomandazione: `MYSQL_PWD`, da provare con il client MariaDB installato.

## Come ripartire

1. `cd laravel && sed -n 1,60p Modules/Setting/app/Actions/DB/DownloadAction.php && sed -n 1,40p Modules/Activity/app/Filament/Pages/LogViewer.php`: rileggere il modello dell'autorizzazione.
2. Creare `DumpDatabaseAction.php` (passi 1-4), poi riscrivere `DownloadAction.php` (5) e `BackupMysql.php` (6): un file alla volta, `php -l` dopo ciascuno.
3. Lanciare il gate PHPStan su `Modules/Setting/app/Actions/DB` e `Modules/Setting/app/Filament/Pages`, poi spuntare gli AC con i comandi della sezione Verifica e annotare qui le cifre con data e ora UTC.

---
title: "Setting: chiusura quality-gate (PHPStan/PHPMD/PHPInsights/Pest/coverage)"
type: story
module: Setting
slug: setting-quality-gate-2026-09-04
status: done
created: 2026-09-04
updated: 2026-09-04
owned_scope:
  - "laravel/Modules/Setting/docs/coverage.md"
  - "laravel/Modules/Setting/docs/stories/setting-quality-gate-2026-09-04.story.md"
related:
  - ../coverage.md
  - ../../../../bashscripts/ai/wiki/rules/pest5-incompatibile-con-phpinsights.md
  - ../../../../bashscripts/ai/wiki/rules/module-work-checklist.md
---

# Setting: chiusura quality-gate (PHPStan/PHPMD/PHPInsights/Pest/coverage)

## Fase BMAD: Build + Measure

Task da standing order (pillar 5, rito modulo): chiudere il quality-gate del
modulo `Setting` — PHPStan gia' verificato a 0 errori in precedenza nella
stessa giornata da un'altra sessione; qui si ri-verifica la baseline e si
esegue il resto del rito (phpmd, phpinsights, pest, coverage, git).

## Cosa e' stato trovato

`Modules/Setting` e' uno scaffold nwidart minimale: 13 file PHP totali
(`SettingController`, 3 Provider standard, `config/config.php`, un seeder
vuoto, 2 layout blade + 1 view, `routes/web.php` e `routes/api.php` entrambi
vuoti, `tests/Pest.php` + `tests/TestCase.php` senza alcun test reale — solo
`.gitkeep` in `tests/Feature/` e `tests/Unit/`). Nessuna business logic
custom: tutto e' boilerplate del generatore di modulo, funzionalmente inerte
(`SettingController` non e' raggiungibile da nessuna rotta registrata).

Anomalia strutturale rispetto a tutti gli altri moduli del monorepo (`User`,
`Job`, `Media`, `Notify`, `Tenant`, verificati per confronto): il `.git`
separato di `Modules/Setting` **non ha nessun commit** ("your current branch
'dev' does not have any commits yet") e **nessun remote configurato**
(`git remote -v` restituisce vuoto). Il modulo non compare nemmeno nella
mappa `docs/chat/github-repos-map.md` (che elenca Activity, AI, Chart,
CloudStorage, Cms, DbForge, Gdpr, Geo, Job, Lang, Limesurvey, Media, Notify,
Quaeris, Tenant, UI, User, Xot, Zero — Setting assente). Non e' stato
investigato oltre ne' "riparato" alla cieca (creare un remote/repo GitHub
unilateralmente e' fuori scope per un task di quality-gate) — vedi sezione
Bloccanti sotto.

## Cosa e' stato fatto

1. `php -l` sweep su tutti i 13 file `.php` del modulo → nessun errore di
   sintassi.
2. `./vendor/bin/phpstan clear-result-cache` + `./vendor/bin/phpstan analyse
   Modules/Setting` → **0 errori** (10 file analizzati, `[OK] No errors`),
   confermata la baseline gia' misurata in precedenza nella stessa giornata.
3. `./tools/phpmd.sh Modules/Setting/app text ../docs/phpmd.ruleset.xml` →
   output vuoto, exit 0. **Zero finding** (nessuna God class, nessuna
   complessita' ciclomatica eccessiva, nessun codice morto rilevato dal
   tool, nessuna violazione di naming). Nessun fix necessario.
4. PHPInsights: `vendor/bin/phpinsights` non esiste in questo repo — verificato,
   coerente con la memoria di progetto `pest5-incompatibile-con-phpinsights`
   (Pest 5 richiede `sebastian/diff ^9`, phpinsights si ferma a `^6|^7`: non
   coesistono, il progetto ha scelto Pest 5 e rimosso phpinsights ovunque).
   Gap pre-esistente e gia' documentato a livello di progetto, non di questo
   modulo: non risolvibile ne' da risolvere dentro un singolo task di
   quality-gate di modulo.
5. Pest: nessun file di test esiste nel modulo. `XDEBUG_MODE=coverage
   ./vendor/bin/pest Modules/Setting/tests` (serve `XDEBUG_MODE=coverage`
   esplicito: `pcov` non e' installato su questa macchina nonostante il
   commento in `phpunit.xml`, altrimenti pest esce silenziosamente senza
   eseguire nulla — memoria `pest-coverage-xdebug-mode-coverage-non-off`) →
   `INFO No tests found.`, exit 0. 0 passed / 0 failed, nessuna regressione
   perche' non c'e' nulla da regredire.
6. Coverage: creato `docs/coverage.md` (non esisteva) con la baseline reale
   (0 test, 0% coverage) e la motivazione per cui NON sono stati aggiunti
   test finti solo per alzare il numero (nessun gap concreto emerso da
   phpmd/phpinsights, unico strumento disponibile — phpmd — pulito). Segnalata
   nel documento una nota strutturale per una story futura: i metodi
   `create()`/`show()`/`edit()` di `SettingController` referenziano view
   (`setting::create`, `setting::show`, `setting::edit`) che non esistono, e
   nessuna rotta punta al controller — non e' un finding di phpmd/phpinsights
   quindi fuori scope per questo task, ma documentato per non perderlo.

## Come e' stato verificato

Numeri reali, misurati in questa sessione il 2026-09-04:

- PHPStan: 0 → 0 (nessuna regressione).
- PHPMD: 0 finding su 4 file in `app/`.
- PHPInsights: non installato (`vendor/bin/phpinsights` assente), gap
  documentato, non imputabile a questo modulo.
- Pest: 0 test trovati, 0 passed, 0 failed, exit 0.
- Coverage: 0% reale, documentato in `docs/coverage.md`, non gonfiato.

## Bloccanti

- **Git**: `Modules/Setting` non ha ne' commit ne' remote configurato,
  diversamente da ogni altro modulo del monorepo. Impossibile eseguire
  `git fetch`/`merge`/`push` come richiesto dal workflow standard perche' non
  esiste alcun remote verso cui operare, e il modulo non e' nemmeno
  censito in `docs/chat/github-repos-map.md`. Non e' stato creato un remote
  o un repository GitHub unilateralmente (decisione infrastrutturale fuori
  scope per un agente di quality-gate). I due file creati da questo task
  (`docs/coverage.md`, `docs/stories/setting-quality-gate-2026-09-04.story.md`)
  sono stati comunque aggiunti e committati **localmente** nel `.git` del
  modulo (primo commit del repo) cosi' che il lavoro non vada perso, ma
  **non pushati da nessuna parte** — serve una sessione dedicata/decisione
  centrale per registrare il repo `laraxot/module_setting_fila5` (o
  equivalente) e agganciare il remote.

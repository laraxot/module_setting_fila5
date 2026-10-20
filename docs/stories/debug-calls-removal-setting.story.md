---
title: "[STORY] Rimozione delle chiamate di debug dal codice di produzione (Setting)"
type: story
status: done
priority: high
created: 2026-10-20
updated: 2026-10-20
module: Setting
tags: [bmad, debug, dddx, filament, table-action, backup, quality]
qmd: "setting dddx DatabaseBackupTableAction backup database DownloadAction BackupMysql LogicException"
related:
  - ../../../User/docs/stories/debug-calls-removal-user.story.md
  - ../bmad/livewire-inventory.md
---

# Rimozione delle chiamate di debug dal codice di produzione (Setting)

## Fase BMAD

Fase di implementazione e verifica (quality gate), parte della wave sulle chiamate che fermano la richiesta (`dddx()` termina
con `dd()`: in produzione stampa un dump con percorsi e dati e uccide la richiesta).

## Sito: `Filament/Actions/Table/DatabaseBackupTableAction.php`

**Scopo capito.** Azione di tabella "Backup Database" (tooltip e icona vengono da `lang/it/database_connection.php`,
`actions.database-backup`) pensata per le righe di `DatabaseConnectionResource`, il cui modello `DatabaseConnection` e' un Sushi
costruito da `config('database.connections')`. Il corpo del `->action()` e' rimasto a `dddx(['record' => $record])`: non e' mai stato
scritto. La classe non e' registrata in nessuna tabella (`DatabaseConnectionsTable::getTableActions()` ha solo `test`) e non ha
chiamanti: verificato con `rg -P` e con `git grep -w`, che trovano solo la definizione e due citazioni nei docs.

Il backup vero esiste gia': `Pages\BackupMysql` chiama `Actions\DB\DownloadAction` (mysqldump piu' gzip, poi download).

**Cosa ho fatto.** Niente cancellazione (classe senza chiamanti: la si segnala). Ho solo disinnescato la bomba: il closure ora fa
`throw new \LogicException('DatabaseBackupTableAction::action() non implementato')`, con un commento che rimanda a `DownloadAction`.
Se qualcuno la registra domani, ottiene un errore esplicito e non un dump.

**Codice riattivato.** Nessuno (dopo il `dddx` non c'era nulla).

**Rischio.** Nullo oggi: la classe non e' raggiungibile da nessuna UI.

## Decisioni aperte

1. **Cancellare o completare.** Completarla e' una riga (`app(DownloadAction::class)->execute($record->name)`), ma esporrebbe un
   dump del database a chi vede la tabella: prima servono una policy su `DatabaseConnection` e il vincolo `driver === 'mysql'`
   (la pagina `BackupMysql` filtra gia' i soli mysql). Altrimenti, cancellare la classe.
2. **Sicurezza di `DownloadAction` (non toccata, non e' debug).** Compone `mysqldump --user=%s --password=%s %s | gzip > %s` con
   valori del config senza `escapeshellarg`: la password finisce nella command line (visibile in `ps`) e un valore con caratteri
   speciali rompe il comando. Da riscrivere con `Process` in forma array e `MYSQL_PWD`.
3. **Manca `docs/bmad/filament-blueprint.md` per Setting** (esiste solo il dossier `livewire-widget-*`). Da creare quando si decide
   la forma finale dell'azione.

## Verifica

- Tokenizer su `Modules/Setting/app`: 0 chiamate reali.
- `php -l` sul file toccato: nessun errore.
- PHPStan gated su `Modules/Setting` intero: exit 0, nessun errore.

---
title: "Inventario Http/Livewire → Filament widget — Setting"
type: inventory
module: Setting
status: approved
track: campaign
related:
  - ./livewire-widget-architecture.md
  - ./livewire-widget-project-context.md
  - ./livewire-widget-decision-log.md
  - ./livewire-widget-epics.md
  - ../../User/docs/bmad/livewire-inventory.md
  - ../../Xot/docs/bmad/livewire-widget-project-context.md
---

# Inventario: Livewire HTTP → Filament — modulo Setting

**Solo documentazione. Nessun PHP toccato in questo audit.**

Questo file è la SSoT del modulo Setting per la campagna di conversione Livewire → Filament widget. Formato e metodo ripresi da [Modules/Cms/docs/bmad/livewire-inventory.md](../../Cms/docs/bmad/livewire-inventory.md).

## Metodo (codice, non assunzione)

```bash
find Modules/Setting/app/Http/Livewire Modules/Setting/app/Livewire -type f -name '*.php'
find Modules/Setting -iname '*livewire*' -not -path '*/vendor/*'
find Modules/Setting -path '*/vendor/*' -prune -o -name '*.php' -print | xargs grep -l 'extends.*\(Component\|Livewire\)'
grep -rn "@livewire" Modules/Setting/resources/views
grep -rln "<livewire:" Modules/Setting/resources/views
find Modules/Setting/app/Filament/Widgets -type f
ls Modules/Setting/resources/views/pages
```

## Classi Livewire trovate: zero

`Modules/Setting/app/Http/Livewire/` contiene solo `_components.json` con contenuto `[]`. `app/Livewire/` non esiste. Il grep `extends.*(Component|Livewire)` su tutti i `.php` del modulo non restituisce file. **Il modulo Setting non possiede alcun componente Livewire.**

## Superficie Filament esistente: solo Resource/Page, zero widget

`find Modules/Setting/app/Filament -type f` mostra:

- `Resources/DatabaseConnectionResource.php` + Pages/Schemas/Tables;
- `Pages/Dashboard.php` e `Pages/BackupMysql.php`;
- `Actions/Table/DatabaseBackupTableAction.php`.

`Modules/Setting/app/Filament/Widgets/` **non esiste**: il `discoverWidgets` di `Modules/Xot/app/Providers/Filament/XotBasePanelProvider.php:134-137` punta a un path assente. Le pagine Filament (Dashboard, BackupMysql) sono già componenti Livewire nel senso Filament — non candidati di conversione, sono la forma finale.

## Verifica del montaggio

| Meccanismo | Dove si cerca | Esito |
|---|---|---|
| `@livewire(...)` nelle viste del modulo | `grep -rn "@livewire" Modules/Setting/resources/views` | Zero hit |
| `<livewire:` nelle viste | `grep -rln "<livewire:" Modules/Setting/resources/views` | Zero hit |
| Render hook nel provider | `Modules/Setting/app/Providers/Filament/AdminPanelProvider.php` (12 righe) | Solo `protected string $module = 'Setting'` (riga 11): nessun hook, nessun widget |
| Rotte | `Modules/Setting/routes/web.php` (18 righe) | Tutto commentato: nessuna rotta reale |
| Folio/Volt | `ls Modules/Setting/resources/views/pages` | Cartella assente (esiste `resources/views/filament/pages/` per le viste delle Filament Page) |

## Classificazione

| Classe | Alias/hook | Gemello widget | Cluster | Nota |
|---|---|---|---|---|
| — | — | — | — | Nessuna classe `Http\Livewire` nel modulo: niente da classificare |

**Cluster A: zero candidati.** **Cluster B: zero candidati.** **Cluster C: zero componenti.**

## Verdetto

Nessuna story di implementazione: zero candidati reali. Le impostazioni restano Resource/Page Filament — già la forma corretta, nessun `Http\Livewire` da convertire né da creare.

## Riferimenti correlati (non SSoT, coerenti col verdetto)

- [livewire-widget-architecture.md](./livewire-widget-architecture.md)
- [livewire-widget-brainstorming.md](./livewire-widget-brainstorming.md)
- [livewire-widget-decision-log.md](./livewire-widget-decision-log.md)
- [livewire-widget-epics.md](./livewire-widget-epics.md)
- [livewire-widget-prd.md](./livewire-widget-prd.md)
- [livewire-widget-product-brief.md](./livewire-widget-product-brief.md)
- [livewire-widget-project-context.md](./livewire-widget-project-context.md)
- [livewire-widget-tech-spec.md](./livewire-widget-tech-spec.md)
- [livewire-widget-ux.md](./livewire-widget-ux.md)

## Successo

- [x] Inventario completo del modulo (0 classi `Http\Livewire`, verificato con find + grep)
- [x] Verifica montaggio in tutto il modulo (provider, blade, rotte, Folio)
- [x] `Filament/Widgets` assente confermato
- [x] Nessuna story di conversione creata (zero candidati reali)

---
title: "ADR del modulo Setting"
type: index
tags: [adr, decisioni, setting]
created: 2026-11-24
updated: 2026-11-24
---

# ADR del modulo Setting

Il **perche'** delle scelte di Setting: una decisione per file, con le alternative scartate. Il nome del file e' l'argomento, senza date ne'
numeri (la data sta nella frontmatter).

Leggi qui prima di cambiare come il modulo gestisce impostazioni, connessioni database e backup.

## Metodo

Scrivere un ADR solo per una scelta difficile da invertire, sorprendente o fondata su un trade-off reale; non e' un diario ne' una
spiegazione retroattiva inventata. Le decisioni si verificano contro codice e test correnti. Una modifica sostanziale crea un nuovo ADR e
segna il precedente `superseded` o `deprecated`. Regole: [action-behavioral-contract](../../../Xot/docs/rules/action-behavioral-contract.md)
e [ADR di Xot](../../../Xot/docs/adr/README.md).

## Formato

```markdown
---
title: "ADR - <decisione in una riga>"
type: decision
status: proposed | accepted | superseded | deprecated
created: AAAA-MM-GG
updated: AAAA-MM-GG
---

# ADR: <decisione>

## Contesto
Fatti e vincoli che hanno reso necessaria la scelta (misure, norme, policy, incidenti).

## Decisione
Cosa si fa, in concreto, e dove sta nel codice.

## Alternative scartate
Ogni alternativa seria, con il motivo per cui non e' stata scelta.

## Conseguenze
Pro, contro, debito che resta, cosa misurare. Chi cambia la decisione scrive un nuovo ADR e segna questo `superseded`.

## Verifica
Comandi o test che esistono e falliscono se la decisione viene violata; solo cio' che si puo' ripetere.
```

Un ADR "sostanziale" aggiunge `## Driver della decisione` e `## Conferma`.

## Decisioni

Nessuna al 2026-11-24. Un ADR non e' stato scritto perche' nelle docs del modulo non c'e' nessuna decisione registrata. `docs/decision-log.md` contiene solo la voce modello «YYYY-MM-DD — Initial commit» scritta da `bmad-init`; `docs/bmad/livewire-widget-decision-log.md` e' un inventario («le impostazioni restano Resource/Page Filament») senza alternative. La story `docs/stories/setting-backup-database-senza-shell.story.md` e' `ready-for-dev` e ha `Decisioni aperte`: quando verra' eseguita, la scelta (password fuori dalla riga di comando, dump senza shell) e' la prima candidata a diventare un ADR.

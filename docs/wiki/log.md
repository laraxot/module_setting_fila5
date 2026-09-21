---
title: "Activity log"
type: log
module: Setting
tags: [setting, phpstan, wiki]
created: 2026-05-11
updated: 2026-09-21
---

# Activity Log — Setting

## [2026-09-21] phpstan | Resource non sovrascrive getFormSchema final

- `DatabaseConnectionResource` non override più `getFormSchema()`/`table()`.
- Schema in `DatabaseConnectionForm` (istanza) e `DatabaseConnectionsTable`.
- Gate: fatal `Cannot override final method XotBaseResource::getFormSchema()`.
- Policy: parametro modello in firma Gate + suppress UnusedFormalParameter (pattern `UiBasePolicy`). PHPMD verde.

## [2026-05-11] Wiki structure created

- Created wiki structure: rules/, skills/, commands/, memories/, concepts/

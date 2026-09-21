# PHPStan — modulo Setting

Misura 2026-09-21: il gate full-tree fatalizzava su
`DatabaseConnectionResource::getFormSchema()` — override di un metodo `final`
istanza su `XotBaseResource`. Lo schema era già in
`Schemas/DatabaseConnectionForm` (duplicato). Rimossi override illegali da
`form`/`table`/`getFormSchema` sulla Resource; `getFormSchema()` del Form è
istanza, non `static`.

SSoT campagna: [phpstan-status.md](../../Xot/docs/phpstan-status.md).

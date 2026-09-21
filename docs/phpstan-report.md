# PHPStan — modulo Setting

Misura 2026-09-21: il gate full-tree fatalizzava su Setting:

1. `DatabaseConnectionResource::getFormSchema()` overrideva un metodo `final` istanza.
2. `DatabaseConnectionForm`/`Infolist` dichiaravano gli hook `static` contro le classi base istanza.
3. 25 errori Setting (ignore orfani, `array` senza value type, `Ptv\Models\Profile` nel PHPDoc, `getTableColumns()` deprecato sulla list page).
4. Marker `<<<<<<<` in `Activity/Pages/LogViewer.php` (e altri) mutavano il bootstrap Filament.

Chiusi. SSoT campagna: [phpstan-status.md](../../Xot/docs/phpstan-status.md).

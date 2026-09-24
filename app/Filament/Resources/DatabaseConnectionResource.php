<?php

declare(strict_types=1);

namespace Modules\Setting\Filament\Resources;

use Modules\Setting\Models\DatabaseConnection;
use Modules\Xot\Filament\Resources\XotBaseResource;

/**
 * Schema form/table vivono in Schemas/DatabaseConnectionForm e
 * Tables/DatabaseConnectionsTable. Qui non si override `form()`/`table()`/
 * `getFormSchema()`: su XotBaseResource `getFormSchema()` è final.
 */
final class DatabaseConnectionResource extends XotBaseResource
{
    protected static ?string $model = DatabaseConnection::class;
}

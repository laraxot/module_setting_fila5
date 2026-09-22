<?php

declare(strict_types=1);

namespace Modules\Setting\Filament\Resources\DatabaseConnectionResource\Pages;

use Modules\Setting\Filament\Resources\DatabaseConnectionResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListDatabaseConnections extends XotBaseListRecords
{
    protected static string $resource = DatabaseConnectionResource::class;
}

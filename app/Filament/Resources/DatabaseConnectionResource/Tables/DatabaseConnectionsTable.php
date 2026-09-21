<?php

declare(strict_types=1);

namespace Modules\Setting\Filament\Resources\DatabaseConnectionResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Modules\Setting\Models\DatabaseConnection;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class DatabaseConnectionsTable extends XotBaseResourceTable
{
    /**
     * @return array<string, CreateAction>
     */
    public function getTableHeaderActions(): array
    {
        return [
            'create' => CreateAction::make(),
        ];
    }

    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'name' => TextColumn::make('name')
                ->searchable()
                ->sortable(),
            'driver' => TextColumn::make('driver')
                ->searchable()
                ->sortable(),
            'host' => TextColumn::make('host')
                ->searchable(),
            'port' => TextColumn::make('port')
                ->numeric()
                ->sortable(),
            'database' => TextColumn::make('database')
                ->searchable()
                ->sortable(),
            'username' => TextColumn::make('username')
                ->searchable(),
            'charset' => TextColumn::make('charset')
                ->searchable(),
            'collation' => TextColumn::make('collation')
                ->searchable(),
            'prefix' => TextColumn::make('prefix')
                ->searchable(),
            'strict' => IconColumn::make('strict')
                ->boolean(),
            'engine' => TextColumn::make('engine')
                ->searchable(),
            'status' => TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                    'testing' => 'warning',
                    default => 'gray',
                }),
            'created_at' => TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            'updated_at' => TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @return array<string, SelectFilter>
     */
    public function getTableFilters(): array
    {
        return [
            'driver' => SelectFilter::make('driver')
                ->options([
                    'mysql' => 'MySQL',
                    'pgsql' => 'PostgreSQL',
                    'sqlite' => 'SQLite',
                    'sqlsrv' => 'SQL Server',
                ]),
            'status' => SelectFilter::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'testing' => 'Testing',
                ]),
        ];
    }

    /**
     * @return array<int|string, Action|ActionGroup>
     */
    public function getTableActions(): array
    {
        $actions = parent::getTableActions();
        $actions['test'] = Action::make('test')
            ->action(function (DatabaseConnection $record): void {
                $record->testConnection();
            })
            ->icon('heroicon-o-check-circle')
            ->color('success');

        return $actions;
    }
}

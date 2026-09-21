<?php

declare(strict_types=1);

namespace Modules\Setting\Filament\Pages;

use Modules\Setting\Actions\DB\DownloadAction;
use Modules\Xot\Filament\Pages\XotBasePage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webmozart\Assert\Assert;

final class BackupMysql extends XotBasePage
{
    protected string $view = 'setting::filament.pages.backup-mysql';

    public function download(string $connectionName): BinaryFileResponse
    {
        return app(DownloadAction::class)->execute($connectionName);
    }

    /**
     * @return array{connections: array<string, array<int|string, mixed>>}
     */
    protected function getViewData(): array
    {
        $connections = config('database.connections');
        Assert::isArray($connections);

        $mysqlConnections = [];
        foreach ($connections as $name => $item) {
            if (! is_string($name) || ! is_array($item)) {
                continue;
            }
            $driver = $item['driver'] ?? null;
            if ($driver === 'mysql') {
                $mysqlConnections[$name] = $item;
            }
        }

        return ['connections' => $mysqlConnections];
    }
}

<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Process;

trait DatabaseBackuper
{
    private function backupDatabase(): void
    {
        Process::start([
            PHP_BINARY,
            'artisan',
            'backup:database',
            0,
        ]);
    }
}

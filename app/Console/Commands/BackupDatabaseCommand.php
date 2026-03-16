<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Libraries\BackupDatabaseLibrary;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

final class BackupDatabaseCommand extends Command
{
    protected $signature = 'backup:database {--s|screen} {--f|force}';

    protected $description = 'Backups the SQLite database file to an SSH host';

    public function __construct(private readonly BackupDatabaseLibrary $backupLibrary)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $forceBackup = (bool) $this->option('force');
        $toScreen = (bool) $this->option('screen');

        $this->backupLibrary->setForceBackup($forceBackup)
            ->setToScreen($toScreen);

        try {
            if ($toScreen) {
                clear();
                info('Database Backup');
            }

            if (! $this->backupLibrary->isBackupHostReachable()) {
                $message = 'Database Backup Host is not reachable';
                if ($toScreen) {
                    warning($message);
                }

                Log::warning($message);

                return self::FAILURE;
            }

            $this->backupLibrary->backupDatabase();

            return self::SUCCESS;
        } catch (Exception $e) {
            if ($toScreen) {
                $this->newLine();
                error($e->getMessage());
            }

            Log::error($e->getMessage());

            return self::FAILURE;
        } finally {
            if ($toScreen) {
                $this->newLine();
                info('Done');
            }
        }
    }
}

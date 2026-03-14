<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Traits\Screenable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use RuntimeException;

final class BackupDatabaseLibrary
{
    use Screenable;

    public function isBackupHostReachable(): bool
    {
        $host = Config::string('backup-database.ssh_host');
        $port = Config::integer('backup-database.ssh_port');
        $timeout = Config::integer('backup-database.ssh_check_timeout');

        $connection = @fsockopen($host, $port, $errorCode, $errorMessage, $timeout);

        if ($connection) {
            fclose($connection);

            return true;
        }

        throw new RuntimeException("{$errorMessage} ({$errorCode})");
    }

    public function backupDatabase(): void
    {
        if (! $this->isTimeForBackup()) {
            return;
        }

        $this->backup();
    }

    private function isTimeForBackup(): bool
    {
        [$archiveFile] = $this->getFiles();

        if (file_exists($archiveFile)) {
            $this->warning('Database already backed up today');

            return false;
        }

        return true;
    }

    private function backup(): void
    {
        [$archivePath, $dbFile] = $this->getFiles();

        $this->notice("Archiving {$dbFile} to {$archivePath}");
        Process::run(['tar', '-czf', $archivePath, $dbFile])
            ->throw();

        if (! file_exists($archivePath)) {
            throw new RuntimeException("{$archivePath} not created");
        }

        if (app()->isLocal()) {
            $this->notice('Skipping remote upload in local environment');

            return;
        }

        $destination = sprintf(
            '%s@%s:%s',
            Config::string('backup-database.ssh_user'),
            Config::string('backup-database.ssh_host'),
            Config::string('backup-database.ssh_backup_path')
        );

        $this->notice("Uploading {$archivePath} to {$destination}");
        Process::run(['rsync', '-auzq', $archivePath, $destination])
            ->throw();
    }

    private function getFiles(): array
    {
        $dbFile = Config::string('database.connections.sqlite.database');
        $archiveFile = sprintf(
            '%s-%s.tar.gz',
            pathinfo($dbFile, PATHINFO_BASENAME),
            now()->format('Y-m-d')
        );
        $archivePath = storage_path("app/private/{$archiveFile}");

        return [$archivePath, $dbFile];
    }
}

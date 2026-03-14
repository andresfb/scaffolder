<?php

declare(strict_types=1);

return [

    'ssh_host' => env('DB_BACKUP_SSH_HOST', '127.0.0.1'),

    'ssh_port' => env('DB_BACKUP_SSH_PORT', 22),

    'ssh_user' => env('DB_BACKUP_SSH_USER'),

    'ssh_backup_path' => env('DB_BACKUP_SSH_BACKUP_PATH'),

    'ssh_check_timeout' => (int) env('DB_BACKUP_SSH_CHECK_TIMEOUT', 5),

];

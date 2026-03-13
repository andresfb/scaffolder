<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Traits\DatabaseBackuper;
use App\Traits\ResolvesActingUser;
use App\Traits\UserLogin;
use Illuminate\Console\Command;

use function Laravel\Prompts\warning;

abstract class BaseUserCommand extends Command
{
    use DatabaseBackuper;
    use ResolvesActingUser;
    use UserLogin;

    protected function loadUser(): User
    {
        $this->backupDatabase();

        $user = $this->resolveActingUser($this->option('user'));
        if (! $user instanceof User) {
            warning('No user found. Logging in...');
            $user = $this->login();
        }

        return $user;
    }
}

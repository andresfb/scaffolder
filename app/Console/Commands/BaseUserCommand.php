<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Traits\ResolvesActingUser;
use App\Traits\UserLogin;
use Illuminate\Console\Command;
use function Laravel\Prompts\warning;

abstract class BaseUserCommand extends Command
{
    use ResolvesActingUser;
    use UserLogin;

    protected function loadUser(): User
    {
        $user = $this->resolveActingUser($this->option('user'));
        if (! $user) {
            warning('No user found. Logging in...');
            $user = $this->login();
        }

        return $user;
    }
}

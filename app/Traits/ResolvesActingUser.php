<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ResolvesActingUser
{
    protected function resolveActingUser(?string $explicitUserId = null): ?User
    {
        $userId = $explicitUserId ?: getenv('SCAFFOLDER_USER_ID');

        if (! $userId || ! ctype_digit((string) $userId)) {
            return null;
        }

        $user = User::query()
            ->with('settings')
            ->where('id', (int) $userId)
            ->first();

        if (! $user) {
            return null;
        }

        Auth::setUser($user);

        return $user;
    }
}

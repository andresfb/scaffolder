<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\form;
use function Laravel\Prompts\warning;

trait UserLogin
{
    private function login(): User
    {
        info('Logging in');

        $response = form()
            ->text(
                label: 'Email',
                default: Config::string('constants.admin_email'),
                required: true,
                validate: 'string|email|max:255',
                name: 'email',
            )
            ->password(
                label: 'Password',
                required: true,
                validate: 'string|min:8|max:255',
                name: 'password',
            )
            ->submit();

        if (! Auth::attempt($response)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = auth()->user();

        warning('To skip the login, set the following ENV variable:');
        $this->newLine();
        $this->line("export SCAFFOLDER_USER_ID=$user->id");
        $this->newLine();

        return $user->load('settings');
    }
}

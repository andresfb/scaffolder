<?php

namespace App\Console\Commands;

use App\Traits\ResolvesActingUser;
use App\Traits\UserLogin;
use Illuminate\Console\Command;
use Exception;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class ScaffoldSketchCommand extends Command
{
    use ResolvesActingUser;
    use UserLogin;

    protected $signature = 'sketch {--user=}';

    protected $description = 'Scaffold a new `Sketch` story using the Prompt Generator API and AI';

    public function handle(): int
    {
        try {
            clear();
            info('Scaffolding Sketch...');

            $user = $this->resolveActingUser($this->option('user'));
            if (! $user) {
                warning('No user found. Logging in...');
                $user = $this->login();
            }

            dump($user->toArray());

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->newLine();
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            info('Done');
        }
    }
}

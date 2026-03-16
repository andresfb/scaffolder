<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\warning;

final class TestAppCommand extends BaseUserCommand
{
    protected $signature = 'test:app {--user=}';

    protected $description = 'Command to run random tests';

    public function handle(): void
    {
        try {
            clear();
            intro('Running tests');
            Log::notice('Running tests');

            $this->loadUser();

        } catch (Exception $e) {
            warning($e->getTraceAsString());
            error($e->getMessage());
            Log::error($e->getMessage());
        } finally {
            $this->newLine();
            outro('Done');
            Log::notice('Done');
        }
    }
}

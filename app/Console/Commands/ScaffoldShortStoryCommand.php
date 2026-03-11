<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

final class ScaffoldShortStoryCommand extends BaseUserCommand
{
    protected $signature = 'sketch {--user=}';

    protected $description = 'Scaffold a new Short Story using the Prompt Generator API and AI';

    public function handle(): int
    {
        try {
            clear();
            info('Scaffolding Sketch...');

            $user = $this->loadUser();

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

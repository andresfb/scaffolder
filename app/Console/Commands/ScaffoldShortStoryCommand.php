<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Factories\TaskFactory;
use Exception;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\warning;

final class ScaffoldShortStoryCommand extends BaseUserCommand
{
    protected $signature = 'sketch {--user=}';

    protected $description = 'Scaffold a new Short Story using the Prompt Generator API and AI';

    public function handle(): int
    {
        try {
            clear();
            $this->newLine(4);
            info('Scaffolding Sketch...');

            $this->loadUser();

            $option = select(
                label: 'What you want to do?',
                options: TaskFactory::getOptions(),
                default: 'prompt',
            );

            $task = TaskFactory::getTask($option);
            $task->setToScreen(true);

            $this->newLine();

            warning('Calling the Agent...');
            $task->handle();

            $task->complete();

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

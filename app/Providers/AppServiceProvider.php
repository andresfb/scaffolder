<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dtos\TaskItem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->bind('tasks', fn ($app): Collection => collect());
        /** @param Collection<TaskItem> $tasks */
        $this->app->resolving('tasks', function (Collection $tasks): void {
            $tasks->push(
                new TaskItem(
                    code: 'prompt',
                    description: 'Scaffold a Random Sketch',
                    task: null,
                )
            );
            $tasks->push(
                new TaskItem(
                    code: 'chat',
                    description: 'Chat with an AI',
                    task: null,
                )
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

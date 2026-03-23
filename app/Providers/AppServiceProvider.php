<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dtos\TaskItem;
use App\Tasks\BuildPremiseSketchTask;
use App\Tasks\BuildRandomSketchTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
                    description: 'Build a Random Sketch',
                    taskClass: BuildRandomSketchTask::class,
                )
            );
            $tasks->push(
                new TaskItem(
                    code: 'chat',
                    description: 'Provide your Premise',
                    taskClass: BuildPremiseSketchTask::class,
                )
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(app()->isLocal());
        DB::prohibitDestructiveCommands(app()->isProduction());
    }
}

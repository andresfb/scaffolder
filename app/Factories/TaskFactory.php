<?php

declare(strict_types=1);

namespace App\Factories;

use App\Dtos\TaskItem;
use App\Interfaces\TaskInterface;
use Illuminate\Support\Collection;
use RuntimeException;

final class TaskFactory
{
    public static function getTasks(): Collection
    {
        $tasks = resolve('tasks');
        if (! $tasks instanceof Collection) {
            throw new RuntimeException('No tasks found');
        }

        return $tasks;
    }

    public static function getOptions(): Collection
    {
        return self::getTasks()->pluck('description', 'code');
    }

    public static function getTask(string $taskCode): TaskInterface
    {
        /** @var TaskItem $taskClass */
        $taskClass = self::getTasks()->where('code', $taskCode)->first();
        if (blank($taskClass)) {
            throw new RuntimeException('Task not found');
        }

        $task = resolve($taskClass->taskClass);
        if (! $task instanceof TaskInterface) {
            throw new RuntimeException("{$taskCode} is not a valid task");
        }

        return $task;
    }
}

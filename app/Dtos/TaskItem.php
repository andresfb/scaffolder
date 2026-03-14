<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Interfaces\TaskInterface;
use Spatie\LaravelData\Data;

final class TaskItem extends Data
{
    public function __construct(
        public string $code,
        public string $description,
        public ?TaskInterface $task,
    ) {}
}

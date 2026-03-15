<?php

declare(strict_types=1);

namespace App\Dtos;

use Spatie\LaravelData\Data;

final class TaskItem extends Data
{
    public function __construct(
        public string $code,
        public string $description,
        public string $taskClass,
    ) {}
}

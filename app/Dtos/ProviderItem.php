<?php

declare(strict_types=1);

namespace App\Dtos;

use Laravel\Ai\Enums\Lab;
use Spatie\LaravelData\Data;

final class ProviderItem extends Data
{
    public function __construct(
        public readonly Lab $lab,
        public readonly string $model,
    ) {}
}

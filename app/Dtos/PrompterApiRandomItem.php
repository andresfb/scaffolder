<?php

declare(strict_types=1);

namespace App\Dtos;

use Spatie\LaravelData\Data;

final class PrompterApiRandomItem extends Data
{
    public function __construct(
        public string $format,
        public string $hash,
        public string $prompt,
        public string $file = ''
    ) {}
}

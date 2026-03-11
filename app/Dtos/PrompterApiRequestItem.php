<?php

declare(strict_types=1);

namespace App\Dtos;

use Spatie\LaravelData\Data;

final class PrompterApiRequestItem extends Data
{
    public function __construct(
        public string $ptr = '',
        public string $format = '',
        public string $hash = '',
    ) {}
}

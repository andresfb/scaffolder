<?php

declare(strict_types=1);

namespace App\Dtos;

use JsonException;
use Spatie\LaravelData\Data;

final class PrompterApiRandomItem extends Data
{
    public function __construct(
        public readonly string $format,
        public readonly string $hash,
        public readonly string $prompt,
        public readonly string $file = ''
    ) {}

    /**
     * @throws JsonException
     */
    public function getFileData(): array
    {
        return json_decode($this->file, true, 512, JSON_THROW_ON_ERROR);
    }
}

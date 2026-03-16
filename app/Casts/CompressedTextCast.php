<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

final class CompressedTextCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $decompressed = gzuncompress($value);

        if ($decompressed === false) {
            throw new RuntimeException("Failed to decompress {$key}.");
        }

        return $decompressed;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $compressed = gzcompress($value, 6);

        if ($compressed === false) {
            throw new RuntimeException("Failed to compress {$key}.");
        }

        return $compressed;
    }
}

<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Enums\PrompterApiFormat;
use Override;
use Spatie\LaravelData\Data;

final class PrompterApiRequestItem extends Data
{
    public function __construct(
        public readonly PrompterApiFormat $format,
        public readonly string $hash = '',
        public readonly string $ptr = '',
    ) {}

    public function withPrompter(string $prompter): self
    {
        return new self(
            $this->format,
            $this->hash,
            $prompter
        );
    }

    #[Override]
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['format'] = $this->format->value;

        return $data;
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class OutlineTemplateService
{
    public function execute(): string
    {
        $fileList = $this->loadFiles();
        if ($fileList->isEmpty()) {
            throw new RuntimeException('No template files found');
        }

        $file = (string) $fileList->random();

        return Storage::disk('outlines')->get($file);
    }

    private function loadFiles(): Collection
    {
        return collect(Storage::disk('outlines')->files())
            ->filter(fn (string $file): bool => str_ends_with($file, '.md'));
    }
}

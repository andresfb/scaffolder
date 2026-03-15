<?php

declare(strict_types=1);

namespace App\Interfaces;

interface TaskInterface
{
    public function handle(): void;

    public function complete(): void;

    public function setToScreen(bool $toScreen): self;
}

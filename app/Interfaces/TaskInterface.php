<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Dtos\TaskResponse;
use App\Models\User;

interface TaskInterface
{
    public function handle(User $user): TaskResponse;
}

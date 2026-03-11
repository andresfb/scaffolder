<?php

declare(strict_types=1);

namespace App\Enums;

enum PrompterApiEndpoints: string
{
    case RANDOM = 'random';
    case MARK_USED = 'mark_used';
}

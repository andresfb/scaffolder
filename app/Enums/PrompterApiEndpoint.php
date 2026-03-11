<?php

declare(strict_types=1);

namespace App\Enums;

enum PrompterApiEndpoint: string
{
    case RANDOM = 'random';
    case MARK_USED = 'mark_used';
}

<?php

declare(strict_types=1);

namespace App\Enums;

enum PrompterApiFormat: string
{
    case JSON = 'json';
    case HTML = 'html';
    case MARKDOWN = 'markdown';
    case MCP = 'mcp';
}

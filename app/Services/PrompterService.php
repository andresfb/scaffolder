<?php

declare(strict_types=1);

namespace App\Services;

use App\Dtos\PrompterApiRandomItem;
use App\Dtos\PrompterApiRequestItem;
use App\Enums\PrompterApiEndpoint;
use App\Enums\PrompterApiFormat;
use App\Libraries\PrompterApiLibrary;
use Exception;

final readonly class PrompterService
{
    public function __construct(
        private PrompterApiLibrary $library,
    ) {}

    /**
     * @throws Exception
     */
    public function random(
        PrompterApiFormat $format = PrompterApiFormat::MCP,
        string $prompter = '',
    ): PrompterApiRandomItem {
        $requestItem = new PrompterApiRequestItem($format);

        if (! blank($prompter)) {
            $requestItem = $requestItem->withPrompter($prompter);
        }

        return $this->library->get(PrompterApiEndpoint::RANDOM, $requestItem);
    }
}

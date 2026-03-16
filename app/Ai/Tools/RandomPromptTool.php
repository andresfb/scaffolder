<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\OutlineTemplate;
use App\Services\PrompterService;
use Exception;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Cache;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use RuntimeException;

final readonly class RandomPromptTool implements Tool
{
    public function __construct(
        private PrompterService $prompterService,
    ) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): string
    {
        return 'Gets a Random Writing Prompt from the Prompter API';
    }

    /**
     * Execute the tool.
     *
     * @throws Exception
     */
    public function handle(Request $request): string
    {
        $maxRunts = 5;
        $runCount = 0;
        $prompt = null;

        while ($runCount < $maxRunts) {
            $runCount++;
            $prompt = $this->prompterService->random();
            if (Cache::has($prompt->hash)) {
                continue;
            }

            break;
        }

        if (blank($prompt)) {
            throw new RuntimeException('Prompt not found');
        }

        $prompt = $prompt->parsePrompt()
            ->withTemplate(
                OutlineTemplate::getRandom()
            );

        Cache::put($prompt->hash, $prompt, now()->addDay());

        return $prompt->clearFile()->toJson();
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'value' => $schema->string()->nullable(),
        ];
    }
}

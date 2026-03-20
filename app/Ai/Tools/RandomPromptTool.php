<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\OutlineTemplate;
use App\Services\PrompterService;
use App\Traits\Screenable;
use Exception;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Cache;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use RuntimeException;

final class RandomPromptTool implements Tool
{
    use Screenable;

    public function __construct(
        private readonly PrompterService $prompterService,
    ) {
        $this->toScreen = true;
    }

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
        Cache::forget(md5('last-prompt'));

        while ($runCount < $maxRunts) {
            $runCount++;

            $this->notice('Calling the Random Prompt API...');

            $prompt = $this->prompterService->random();
            if (Cache::has($prompt->hash)) {
                $this->warning("We already used this Prompt: {$prompt->title} today");

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

        $this->notice('Prompt is ready for the AI');

        Cache::put(
            md5('last-prompt'),
            $prompt->hash,
            now()->addDay()->addMinute()
        );

        $saved = Cache::put($prompt->hash, $prompt->getFileData(), now()->addDay());
        if (! $saved) {
            $this->warning('Could not save prompt file to cache');
            Cache::forget(md5('last-prompt'));
        }

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

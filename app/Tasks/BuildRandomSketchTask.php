<?php

declare(strict_types=1);

namespace App\Tasks;

use App\Ai\Agents\RandomSketchBuilderAgent;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Laravel\Ai\Responses\AgentResponse;
use RuntimeException;

final class BuildRandomSketchTask extends BaseSketchTask
{
    public function getAgentResponse(): AgentResponse
    {
        $this->notice('Asking the AI');

        return RandomSketchBuilderAgent::make()
            ->prompt(
                prompt: 'Build a Sketch',
                provider: $this->provider,
                model: $this->model,
                timeout: 180 // 3 minutes
            );
    }

    protected function savePrompt(string $promptPath): void
    {
        try {
            $file = $this->getCachedFileData();
            if (blank($file['base64'])) {
                throw new RuntimeException('Prompt has no base64 data');
            }

            $data = base64_decode($file['base64']);
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return;
        }

        File::put("{$promptPath}/prompt.md", $data);
        Cache::forget($this->response['hash']);
        Cache::forget(md5('last-prompt'));
    }

    private function getCachedFileData(): array
    {
        $prompt = Cache::get($this->response['hash']);
        if (filled($prompt)) {
            return $prompt;
        }

        $hash = Cache::get(md5('last-prompt'));
        if (blank($hash)) {
            throw new RuntimeException('The Prompt file data is missing from the cache');
        }

        $prompt = Cache::get($hash);
        if (filled($prompt)) {
            return $prompt;
        }

        throw new RuntimeException('The Prompt file data is missing from the cache');
    }
}

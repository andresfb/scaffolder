<?php

declare(strict_types=1);

namespace App\Tasks;

use App\Ai\Agents\SketchBuilderAgent;
use App\Factories\ProviderFactory;
use App\Interfaces\TaskInterface;
use App\Traits\AiNotifiable;
use App\Traits\Screenable;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\AgentResponse;
use RuntimeException;

final class BuildSketchTask implements TaskInterface
{
    use AiNotifiable;
    use Screenable;

    private string $model = '';

    private Lab $provider = Lab::OpenAI;

    private AgentResponse $response;

    private array $folders = [];

    public function handle(): void
    {
        $providerItem = ProviderFactory::getRandom();
        $this->provider = $providerItem->lab;
        $this->model = $providerItem->model;

        $this->notice("Using {$this->provider->name} with model: {$providerItem->model}");

        $this->response = SketchBuilderAgent::make()
            ->prompt(
                prompt: 'Build a Sketch',
                provider: $this->provider,
                model: $this->model,
                timeout: 180 // 3 minutes
            );

        if (blank($this->response['outline'])) {
            throw new RuntimeException('The Agent did not generate an outline');
        }

        $this->saveFiles();
    }

    public function complete(): void
    {
        $this->notify(
            usage: $this->response->usage,
            service: self::class,
            client: $this->provider->name,
            model: $this->model,
            title: 'Story Outline',
        );

        $this->line();
        $this->info("The agent used {$this->provider->name} with model: {$this->model} to scaffold the story.");
        $this->info("It titled it: {$this->response['title']}");
        $this->info('Files located at:');

        $this->line();
        foreach ($this->folders as $key => $folder) {
            $this->notice("{$key} - {$folder}");
        }

        $this->line(2);
        $this->warning('Tokens used:');
        $this->notice("{$this->response->usage->promptTokens} prompt tokens");
        $this->notice("{$this->response->usage->completionTokens} completion tokens");
        $this->notice("{$this->response->usage->reasoningTokens} reasoning tokens");
        $this->warning(sprintf(
            'Total: %s',
            $this->response->usage->promptTokens
                + $this->response->usage->completionTokens
                + $this->response->usage->reasoningTokens
        ));
        $this->line();
    }

    private function saveFiles(): void
    {
        $titlePath = str($this->response['title'])
            ->slug()
            ->toString();

        $destination = sprintf(
            getenv('SCAFFOLDER_SKETCHES_DESTINATION') ?: Config::string('sketches.destination'),
            $titlePath,
        );

        $this->folders = [
            'outline' => "{$destination}/Outline",
            'prompt' => "{$destination}/Prompt",
            'draft' => "{$destination}/Drafts",
        ];

        foreach ($this->folders as $folder) {
            File::ensureDirectoryExists($folder);
        }

        $this->saveOutline($this->folders['outline']);
        $this->saveDraft($this->folders['draft']);
        $this->savePrompt($this->folders['prompt']);
    }

    private function saveOutline(string $outlinePath): void
    {
        File::put("{$outlinePath}/outline.md", $this->response['outline']);
    }

    private function savePrompt(string $promptPath): void
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

    private function saveDraft(string $draftPath): void
    {
        if (blank($this->response['draft'])) {
            $this->error('The agent did not generate a draft');

            return;
        }

        File::put("{$draftPath}/draft.md", $this->response['draft']);
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

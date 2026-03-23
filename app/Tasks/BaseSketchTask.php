<?php

namespace App\Tasks;

use App\Factories\ProviderFactory;
use App\Interfaces\TaskInterface;
use App\Traits\AiNotifiable;
use App\Traits\Screenable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\AgentResponse;
use RuntimeException;

abstract class BaseSketchTask implements TaskInterface
{
    use AiNotifiable;
    use Screenable;

    protected string $model = '';

    protected array $folders = [];

    protected Lab $provider = Lab::OpenAI;

    protected AgentResponse $response;

    abstract public function getAgentResponse(): AgentResponse;

    abstract protected function savePrompt(string $promptPath): void;

    public function handle(): void
    {
        $providerItem = ProviderFactory::getRandom();
        $this->provider = $providerItem->lab;
        $this->model = $providerItem->model;

        $this->notice("Using {$this->provider->name} with model: {$providerItem->model}");

        $this->response = $this->getAgentResponse();

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

    private function saveDraft(string $draftPath): void
    {
        if (blank($this->response['draft'])) {
            $this->error('The agent did not generate a draft');

            return;
        }

        File::put("{$draftPath}/draft.md", $this->response['draft']);
    }
}

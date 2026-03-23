<?php

namespace App\Tasks;

use App\Ai\Agents\PremiseSketchBuilderAgent;
use App\Models\OutlineTemplate;
use Laravel\Ai\Files;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Responses\AgentResponse;
use Override;
use Random\RandomException;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\textarea;

class BuildPremiseSketchTask extends BaseSketchTask
{
    private string $prompt = '';

    private string $templateFile = '';

    /**
     * @throws RandomException
     */
    #[Override]
    public function handle(): void
    {
        $this->prompt = textarea(
            label: 'Enter your premise',
            required: true,
            validate: 'string|required|min:100',
            rows: 20,
        );

        $this->getTemplate();

        parent::handle();
    }

    public function getAgentResponse(): AgentResponse
    {
        $this->notice('Asking the AI');

        return  PremiseSketchBuilderAgent::make()
            ->prompt(
                prompt: $this->prompt,
                attachments: [
                    Files\Document::fromPath($this->templateFile)
                ],
                provider: $this->provider,
                model: $this->model,
                timeout: 180 // 3 minutes
            );
    }

    protected function savePrompt(string $promptPath): void
    {
        File::put("{$promptPath}/prompt.md", $this->prompt);
    }

    /**
     * @throws RandomException
     */
    private function getTemplate(): void
    {
        if (confirm('Use a random template?', false)) {
            $template = OutlineTemplate::getRandom();
            $this->notice("Using Outline Template: {$template->title}");
            $this->templateFile = $this->loadTemplateFile($template);

            return;
        }

        $selection = select(
            label: 'Select a Template',
            options: OutlineTemplate::getList(),
            scroll: 7,
        );

        $template = OutlineTemplate::query()
            ->where('id', (int) $selection)
            ->first();

        $this->templateFile = $this->loadTemplateFile($template);
    }

    private function loadTemplateFile(OutlineTemplate $template): string
    {
        $path = Storage::disk('templates')
            ->path("{$template->id}/{$template->hash}.md");

        if (File::exists($path)) {
            return $path;
        }

        $path = Storage::disk('templates')
            ->path((string) $template->id);

        File::ensureDirectoryExists($path);
        $file = "{$path}/{$template->hash}.md";
        File::put($file, $template->text);

        return $file;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\PrompterApiFormat;
use App\Services\PrompterService;
use Exception;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

final class GetPromptCommand extends BaseUserCommand
{
    protected $signature = 'get:prompt
                            {--user=}
                            {--p|prompter= : Request a specific Prompter}
                            {--f|format= : Output data in MCP (default), JSON, HTML, or Markdown (MD) format}';

    protected $description = 'Selects a random Prompt from the API';

    private bool $showMessages = true;

    public function handle(PrompterService $service): int
    {
        try {
            $formatOption = $this->option('format') ?? 'mcp';
            $format = PrompterApiFormat::from($formatOption);
            $prompter = $this->option('prompter') ?? '';
            $this->showMessages = $format !== PrompterApiFormat::JSON;

            if ($this->showMessages) {
                clear();
                info('Getting Random Prompt');
            }

            $this->loadUser();

            if ($this->showMessages) {
                warning('Asking the API');
            }

            $response = $service->random($format, $prompter);
            $responseFormat = PrompterApiFormat::from($response->format);

            if ($responseFormat === PrompterApiFormat::MARKDOWN) {
                $this->line(str($response->prompt)
                    ->replace('###', '#')
                    ->replace('##', '#')
                    ->trim()
                    ->toString());

                return self::SUCCESS;
            }

            $this->line($response->prompt);

            if ($responseFormat !== PrompterApiFormat::MCP) {
                return self::SUCCESS;
            }

            $this->newLine();
            if (blank($response->file) || ! $this->confirm('Save Markdown File', true)) {
                return self::SUCCESS;
            }

            $content = base64_decode($response->getFileData()['base64']);
            $path = sprintf('%s/Downloads/file.md', getenv('HOME'));
            file_put_contents($path, $content);

            info("File saved to {$path}");

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->newLine();
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();

            if ($this->showMessages) {
                info('Done');
            }
        }
    }
}

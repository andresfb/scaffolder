<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\OutlineTemplate;
use Exception;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

final class EditOutlineTemplatesCommand extends BaseUserCommand
{
    protected $signature = 'edit:templates {--user=}';

    protected $description = "Edit the User's Outline Templates";

    public function handle(): int
    {
        try {
            clear();
            intro('Edit Templates');

            $user = $this->loadUser();
            $templates = OutlineTemplate::query()
                ->where('user_id', $user->id)
                ->orderBy('id')
                ->get();

            $templates->each(function (OutlineTemplate $template) {
                $responses = form()
                    ->textarea(
                        label: 'Template',
                        default: $template->text,
                        required: true,
                        validate: fn (string $value): ?string => mb_strlen($value) < 100
                            ? 'The template must be at least 100 characters.'
                            : null,
                        hint: 'Markdown',
                        rows: 50,
                        name: 'text',
                    )
                    ->text(
                        label: 'Title',
                        default: $template->title ?? '',
                        required: true,
                        validate: 'string|required|min:3',
                        name: 'title',
                    )
                    ->text(
                        label: 'Weight',
                        default: (string) $template->weight ?: '1',
                        required: true,
                        validate: 'numeric|required|between:1,1000',
                        name: 'weight',
                    )
                    ->submit();

                $template->update($responses);
            });

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->newLine();
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }
}

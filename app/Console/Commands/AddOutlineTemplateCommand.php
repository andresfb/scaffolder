<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\OutlineTemplate;
use Exception;
use RuntimeException;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\textarea;

final class AddOutlineTemplateCommand extends BaseUserCommand
{
    protected $signature = 'add:template {--user=}';

    protected $description = 'Adds a new outline template';

    public function handle(): int
    {
        try {
            clear();
            intro('Add Outline Template');

            $user = $this->loadUser();

            $entry = textarea(
                label: 'Template',
                required: true,
                validate: 'string|required|min:100',
                hint: 'Markdown',
                rows: 50,
            );

            $hash = md5($entry);
            $exists = OutlineTemplate::query()
                ->where('user_id', $user->id)
                ->where('hash', $hash)
                ->exists();

            if ($exists) {
                throw new RuntimeException('This template already exists.');
            }

            $outline = new OutlineTemplate;
            $outline->user_id = $user->id;
            $outline->hash = $hash;
            $outline->text = $entry;
            $outline->save();

            return self::SUCCESS;
        } catch (Exception $e) {
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }
}

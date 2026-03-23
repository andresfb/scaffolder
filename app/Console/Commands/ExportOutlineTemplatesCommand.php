<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\OutlineTemplate;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

final class ExportOutlineTemplatesCommand extends BaseUserCommand
{
    protected $signature = 'export:templates {--user=}';

    protected $description = "Export the User's Outline Templates";

    public function handle(): int
    {
        try {
            clear();
            intro('Exporting all templates');

            $user = $this->loadUser();

            $templates = OutlineTemplate::query()
                ->where('user_id', $user->id)
                ->get();

            warning("Found {$templates->count()} templates");
            info(sprintf(
                'Saving files to: %s',
                Storage::disk('templates')->path('')
            ));

            spin(
                fn () => $this->export($templates),
                'Saving files...'
            );

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

    /**
     * @param  Collection<int, OutlineTemplate>  $templates
     */
    private function export(Collection $templates): void
    {
        $templates->each(function (OutlineTemplate $template): void {
            $path = Storage::disk('templates')->path((string) $template->id);
            File::ensureDirectoryExists($path);
            $file = "{$path}/{$template->hash}.md";
            File::put($file, $template->text);
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\UserSetting;
use Exception;
use Illuminate\Support\Facades\Config;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

final class AddUserSettingCommand extends BaseUserCommand
{
    protected $signature = 'add:setting {--user=}';

    protected $description = 'Add a User Setting';

    public function handle(): int
    {
        try {
            clear();
            intro('Add User Setting');

            $user = $this->loadUser();

            $results = form()
                ->text(
                    label: 'Key Name',
                    default: Config::string('prompter-api.api_key_name'),
                    required: true,
                    validate: 'string|max:255',
                    name: 'key',
                )
                ->textarea(
                    label: 'Value',
                    required: true,
                    rows: 10,
                    name: 'value',
                )
                ->submit();

            $this->line('Saving User Setting');

            UserSetting::query()
                ->updateOrCreate([
                    'user_id' => $user->id,
                    'key' => $results['key'],
                ], [
                    'value' => $results['value'],
                ]);

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

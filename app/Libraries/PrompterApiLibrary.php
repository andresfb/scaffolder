<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Dtos\PrompterApiRandomItem;
use App\Dtos\PrompterApiRequestItem;
use App\Enums\PrompterApiEndpoints;
use App\Models\User;
use App\Models\UserSetting;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class PrompterApiLibrary
{
    private array $endpoints = [
        PrompterApiEndpoints::RANDOM->value => 'prompt',
        PrompterApiEndpoints::MARK_USED->value => 'mark_used',
    ];

    /**
     * @throws Exception
     */
    public function get(
        PrompterApiEndpoints $endpoint,
        ?PrompterApiRequestItem $requestItem = null
    ): PrompterApiRandomItem {
        $query = ['format' => 'mcp'];
        if ($requestItem instanceof PrompterApiRequestItem) {
            $query = http_build_query($requestItem->toArray());
        }

        $url = sprintf(
            Config::string('prompter-api.base_uri'),
            $this->endpoints[$endpoint->value]
        );

        $response = Http::withToken($this->getToken())
            ->connectTimeout(30)
            ->timeout(60)
            ->acceptJson()
            ->contentType('application/json')
            ->get($url, $query)
            ->object();

        if ($response === null) {
            throw new RuntimeException('Failed to get response from Prompter API');
        }

        return PrompterApiRandomItem::from($response->data);
    }

    /** @noinspection PhpRedundantVariableDocTypeInspection */
    private function getToken(): string
    {
        $user = User::query()
            ->with('settings')
            ->where('id', auth()->id())
            ->firstOrFail();

        /** @var UserSetting $setting */
        $setting = $user->settings
            ->where(
                'key',
                Config::string('prompter-api.api_key_name')
            )
            ->firstOrFail();

        return $setting->value;
    }
}

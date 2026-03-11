<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Dtos\PrompterApiRandomItem;
use App\Dtos\PrompterApiRequestItem;
use App\Enums\PrompterApiEndpoint;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class PrompterApiLibrary
{
    private array $endpoints = [
        PrompterApiEndpoint::RANDOM->value => 'prompt',
        PrompterApiEndpoint::MARK_USED->value => 'mark_used',
    ];

    /**
     * @throws Exception
     */
    public function get(
        PrompterApiEndpoint $endpoint,
        ?PrompterApiRequestItem $requestItem = null
    ): PrompterApiRandomItem {
        $query = $requestItem instanceof PrompterApiRequestItem
            ? http_build_query($requestItem->toArray())
            : ['format' => 'mcp'];

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
            ->throw()
            ->object();

        if ($response === null) {
            throw new RuntimeException('Failed to get response from Prompter API');
        }

        return PrompterApiRandomItem::from($response->data);
    }

    private function getToken(): string
    {
        $user = User::query()
            ->with('settings')
            ->where('id', auth()->id())
            ->firstOrFail();

        $setting = $user->settings
            ->where(
                'key',
                Config::string('prompter-api.api_key_name')
            )
            ->firstOrFail();

        return $setting->value;
    }
}

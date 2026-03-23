<?php

declare(strict_types=1);

namespace App\Factories;

use App\Dtos\ProviderItem;
use Illuminate\Support\Facades\Config;
use Laravel\Ai\Enums\Lab;

final class ProviderFactory
{
    public static function getProvider(string $code): ProviderItem
    {
        $providers = Config::collection('constants.providers');
        $provider = $providers->where('enabled', true)
            ->where('code', $code)
            ->firstOrFail();

        $lab = Lab::from($provider['code']);

        return new ProviderItem(
            lab: $lab,
            model: (string) collect($provider['models'])->random(),
        );
    }

    public static function getRandom(): ProviderItem
    {
        $providers = Config::collection('constants.providers');
        $provider = $providers->where('enabled', true)->random();
        $lab = Lab::from($provider['code']);

        return new ProviderItem(
            lab: $lab,
            model: (string) collect($provider['models'])->random(),
        );
    }

    public static function getList(): array
    {
        $providers = Config::collection('constants.providers');

        return $providers->where('enabled', true)
            ->sortBy('name')
            ->pluck('name', 'code')
            ->toArray();
    }
}

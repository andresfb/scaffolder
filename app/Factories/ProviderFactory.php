<?php

declare(strict_types=1);

namespace App\Factories;

use App\Dtos\ProviderItem;
use Illuminate\Support\Facades\Config;
use Laravel\Ai\Enums\Lab;

final class ProviderFactory
{
    public static function getRandom(): ProviderItem
    {
        $providers = Config::collection('constants.providers');
        $provider = $providers->where('enabled', true)->random();
        $lab = Lab::from($provider['name']);

        return new ProviderItem(
            lab: $lab,
            model: (string) collect($provider['models'])->random(),
        );
    }
}

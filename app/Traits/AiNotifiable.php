<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Laravel\Ai\Responses\Data\Usage;

trait AiNotifiable
{
    public function notify(
        Usage $usage,
        string $service,
        string $client,
        string $model,
        string $title,
    ): void {

        $totalTokens = $usage->completionTokens + $usage->promptTokens + $usage->reasoningTokens;
        $cost = sprintf('Total tokens %d', $totalTokens);
        $serviceClass = str($service)->classBasename()->toString();

        $message = sprintf(
            '%s used %s with model %s to generate %s. %s',
            $serviceClass,
            $client,
            $model,
            $title,
            $cost,
        );

        User::notification($service, $client, $totalTokens, $message);
    }
}

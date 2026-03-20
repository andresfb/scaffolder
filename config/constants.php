<?php

declare(strict_types=1);

return [

    'admin_name' => env('ADMIN_NAME'),

    'admin_email' => env('ADMIN_EMAIL'),

    'providers' => [
        [
            'name' => 'anthropic',
            'models' => explode(',', env('ANTHROPIC_MODELS')),
            'enabled' => (bool) env('ANTHROPIC_ENABLED'),
        ],
        [
            'name' => 'gemini',
            'models' => explode(',', env('GEMINI_MODELS')),
            'enabled' => (bool) env('GEMINI_ENABLED'),
        ],
        [
            'name' => 'openai',
            'models' => explode(',', env('OPENAI_MODELS')),
            'enabled' => (bool) env('OPENAI_ENABLED'),
        ],
        [
            'name' => 'openrouter',
            'models' => explode(',', env('OPENROUTER_MODELS')),
            'enabled' => (bool) env('OPENROUTER_ENABLED'),
        ],
        [
            'name' => 'ollama',
            'models' => explode(',', env('OLLAMA_MODELS')),
            'enabled' => (bool) env('OLLAMA_ENABLED'),
        ],
    ],

];

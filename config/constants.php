<?php

declare(strict_types=1);

return [

    'admin_name' => env('ADMIN_NAME'),

    'admin_email' => env('ADMIN_EMAIL'),

    'providers' => [
        [
            'name' => 'anthropic',
            'models' => explode(',', env('ANTHROPIC_MODELS')),
        ],
        [
            'name' => 'gemini',
            'models' => explode(',', env('GEMINI_MODELS')),
        ],
        [
            'name' => 'openai',
            'models' => explode(',', env('OPENAI_MODELS')),
        ],
        [
            'name' => 'openrouter',
            'models' => explode(',', env('OPENROUTER_MODELS')),
        ],
        //        [
        //            'name' => 'ollama',
        //            'models' => explode(',', env('OLLAMA_MODELS')),
        //        ],
    ],

];

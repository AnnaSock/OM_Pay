<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'L5 Swagger UI',
                'schemes' => ['https'], // Optionnel selon version OpenAPI
                'servers' => [
                    [
                        'url' => env('L5_SWAGGER_CONST_HOST', 'https://annasock-ompay.onrender.com/api'),
                        'description' => 'Production server',
                    ],
                ],
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
                'annotations' => [
                    'app/Http/Controllers/Api/V1',
                ],
            ],
        ],
    ],
    'securityDefinitions' => [
        'securitySchemes' => [
            'passport' => [
                'type' => 'oauth2',
                'description' => 'Laravel passport oauth2 security.',
                'in' => 'header',
                'scheme' => 'https',
                'flows' => [
                    "password" => [
                        "authorizationUrl" => config('app.url') . '/oauth/authorize',
                        "tokenUrl" => config('app.url') . '/oauth/token',
                        "refreshUrl" => config('app.url') . '/token/refresh',
                    ],
                ],
            ],
        ],
    ],
    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
];


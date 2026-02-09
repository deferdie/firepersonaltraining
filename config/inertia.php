<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inertia SSR
    |--------------------------------------------------------------------------
    |
    | This option determines if Inertia Server-Side Rendering (SSR) is enabled.
    | When enabled, Inertia will pre-render pages on the server before sending
    | them to the client. This can improve initial page load performance.
    |
    */

    'ssr' => [
        'enabled' => false,
        'url' => env('INERTIA_SSR_URL', 'http://127.0.0.1:13714'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Inertia Testing
    |--------------------------------------------------------------------------
    |
    | The testing configuration options allow you to configure how Inertia
    | handles testing assertions. You can configure the assertions that
    | Inertia makes when testing your application.
    |
    */

    'testing' => [
        'ensure_pages_exist' => true,
        'page_paths' => [
            resource_path('js/Pages'),
        ],
        'component_paths' => [
            resource_path('js/Components'),
        ],
    ],
];

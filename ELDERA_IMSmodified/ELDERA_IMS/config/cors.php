<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Specify explicit origins as examples; wildcard ports must use patterns below
    'allowed_origins' => [
        'http://localhost:3000', // React development
        'http://localhost:8080', // Vue development
        'http://127.0.0.1:3000',
        'http://127.0.0.1:8080',
        'http://192.168.1.115:8000', // Laravel server IP
        // Add your production Flutter app domain here
        // 'https://your-flutter-app.com',
    ],

    // Allow any localhost/127.0.0.1 port for development (Flutter web dev server)
    'allowed_origins_patterns' => [
        '/^http:\/\/localhost:\\d+$/',
        '/^http:\/\/127\\.0\\.0\\.1:\\d+$/'
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
<?php

return [
    'app' => [
        'name' => 'Телефонна книга',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
        'debug' => $_ENV['APP_DEBUG'] ?? true,
    ],

    'session' => [
        'lifetime' => 24 * 60 * 60,
        'name' => 'phonebook_session',
    ],

    'remember' => [
        'lifetime' => 30 * 24 * 60 * 60,
        'cookie_name' => 'phonebook_remember',
    ],

    'upload' => [
        'path' => __DIR__ . '/../public/uploads/avatars/',
        'url' => '/uploads/avatars/',
        'max_size' => 5 * 1024 * 1024,
        'allowed_types' => ['image/jpeg', 'image/png'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png'],
    ],

    'pagination' => [
        'per_page' => 9,
    ],
];

<?php

return [
    'db' => [
        'host' => 'db',
        'user' => 'root',
        'password' => 'root',
        'database' => 'cardicom',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ],
    'app' => [
        'name' => 'ELDIR',
        'version' => '1.0.0',
        'url' => '/public',
        'debug' => true,
        'environment' => 'development',
        'timezone' => 'Europe/Moscow',
        'locale' => 'ru_RU'
    ],
    'session' => [
        'name' => 'eldir_session',
        'lifetime' => 7200,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ],
    'mail' => [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'your_username',
        'password' => 'your_password',
        'encryption' => 'tls',
        'from' => [
            'address' => 'noreply@eldir.com',
            'name' => 'ELDIR'
        ]
    ],
    'auth' => [
        'redirect_after_login' => '/dashboard',
        'redirect_after_logout' => '/login',
    ],
    'cache' => [
        'driver' => 'file',
        'prefix' => 'eldir_',
        'path' => __DIR__ . '/../../storage/cache',
    ],
    'uploads' => [
        'path' => __DIR__ . '/../../storage/uploads',
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'max_size' => 5242880,
    ]
]; 
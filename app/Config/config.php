<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'user' => getenv('DB_USER') ?: 'cardicom_user',
        'password' => getenv('DB_PASSWORD') ?: 'cardicom_pass',
        'database' => getenv('DB_NAME') ?: 'cardicom',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ],
    'app' => [
        'name' => 'ELDIR',
        'debug' => true,
        'url' => 'http://localhost',
        'timezone' => 'Europe/Moscow',
        'locale' => 'ru_RU'
    ],
    'session' => [
        'name' => 'ELDIR_SESSION',
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ]
]; 
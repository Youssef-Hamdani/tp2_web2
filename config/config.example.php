<?php

declare(strict_types=1);

return [
    'app' => [
        'base_url' => 'https://votre-site.example',
        'debug' => false,
        'force_https' => true,
        'storage_path' => __DIR__ . '/../storage',
        'product_upload_path' => __DIR__ . '/../uploads/products',
        'product_upload_url' => '/uploads/products',
    ],
    'database' => [
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=tp2_web;charset=utf8mb4',
        'username' => 'tp2_user',
        'password' => 'motdepasse',
    ],
    'mail' => [
        'from_address' => 'noreply@example.com',
        'from_name' => 'TP2 Web',
        'debug_copy_to_storage' => true,
    ],
    'stripe' => [
        'secret_key' => 'sk_test_xxx',
        'publishable_key' => 'pk_test_xxx',
        'webhook_secret' => '',
        'currency' => 'cad',
    ],
    'auth' => [
        'remember_cookie' => 'tp2_remember',
        'remember_for_seconds' => 2592000,
        'activation_for_seconds' => 3600,
        'reset_for_seconds' => 3600,
        'min_password_length' => 10,
    ],
    'session' => [
        'name' => 'TP2MarcheSession',
        'refresh_seconds' => 900,
    ],
    'pricing' => [
        'gst_rate' => 0.05,
        'qst_rate' => 0.09975,
        'stripe_percent' => 0.029,
        'stripe_fixed_cents' => 30,
        'minimum_fee_cents' => 45,
    ],
];

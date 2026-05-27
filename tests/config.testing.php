<?php

declare(strict_types=1);

return [
    'app' => [
        'base_url' => 'http://localhost',
        'debug' => true,
        'force_https' => false,
        'storage_path' => dirname(__DIR__) . '/storage',
        'product_upload_path' => dirname(__DIR__) . '/uploads/products',
        'product_upload_url' => '/uploads/products',
    ],
    'database' => [
        'dsn' => 'sqlite::memory:',
        'username' => '',
        'password' => '',
    ],
    'mail' => [
        'from_address' => 'noreply@example.test',
        'from_name' => 'Tests',
        'debug_copy_to_storage' => false,
    ],
    'stripe' => [
        'secret_key' => '',
        'publishable_key' => '',
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
        'name' => 'TP2TestSession',
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

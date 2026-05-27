<?php

declare(strict_types=1);

$baseUrl = getenv('APP_BASE_URL') ?: 'https://6269176.techinfojoliette.ca';
$root = dirname(__DIR__);

return [
    'app' => [
        'name' => 'TP2 Web',
        'base_url' => $baseUrl,
        'debug' => getenv('APP_DEBUG') === '1',
        'force_https' => str_starts_with($baseUrl, 'https://'),
        'storage_path' => $root . '/storage',
        'product_upload_path' => $root . '/uploads/products',
        'product_upload_url' => '/uploads/products',
    ],
    'database' => [
        'dsn' => getenv('DB_DSN') ?: 'mysql:host=dev02.host.hcu.cloud;port=3306;dbname=u6269176_tp1;charset=utf8mb4',
        'username' => getenv('DB_USERNAME') ?: 'u6269176_codexdb',
        'password' => getenv('DB_PASSWORD') ?: 'Tp1Codex6269176Db2026!',
    ],
    'mail' => [
        'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@6269176.techinfojoliette.ca',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'TP2 Web',
        'debug_copy_to_storage' => getenv('MAIL_DEBUG_STORAGE') !== '0',
    ],
    'stripe' => [
        'secret_key' => getenv('STRIPE_SECRET_KEY') ?: '',
        'publishable_key' => getenv('STRIPE_PUBLISHABLE_KEY') ?: '',
        'webhook_secret' => getenv('STRIPE_WEBHOOK_SECRET') ?: '',
        'currency' => 'cad',
    ],
    'auth' => [
        'remember_cookie' => 'tp2_remember',
        'remember_for_seconds' => 60 * 60 * 24 * 30,
        'activation_for_seconds' => 60 * 60,
        'reset_for_seconds' => 60 * 60,
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

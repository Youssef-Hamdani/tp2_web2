<?php

declare(strict_types=1);

$baseUrl = getenv('APP_BASE_URL') ?: 'https://6269176.techinfojoliette.ca';
$root = dirname(__DIR__);
$localConfig = [];

if (is_file(__DIR__ . '/local.php')) {
    $loadedLocalConfig = require __DIR__ . '/local.php';
    if (is_array($loadedLocalConfig)) {
        $localConfig = $loadedLocalConfig;
    }
}

$config = [
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
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=u6269176_tp2;charset=utf8mb4',
        'username' => 'u6269176_tp2app',
        'password' => 'CHANGE_ME',
    ],
    'mail' => [
        'from_address' => 'noreply@6269176.techinfojoliette.ca',
        'from_name' => 'Site Vente',
        'debug_copy_to_storage' => true,
    ],
    'stripe' => [
        'secret_key' => '',
        'publishable_key' => '',
        'webhook_secret' => '',
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

$config = array_replace_recursive($config, $localConfig);

$config['app']['base_url'] = $baseUrl;
$config['app']['debug'] = getenv('APP_DEBUG') === '1' || (bool) ($config['app']['debug'] ?? false);
$config['app']['force_https'] = str_starts_with($baseUrl, 'https://');
$config['database']['dsn'] = getenv('DB_DSN') ?: $config['database']['dsn'];
$config['database']['username'] = getenv('DB_USERNAME') ?: $config['database']['username'];
$config['database']['password'] = getenv('DB_PASSWORD') ?: $config['database']['password'];
$config['mail']['from_address'] = getenv('MAIL_FROM_ADDRESS') ?: $config['mail']['from_address'];
$config['mail']['from_name'] = getenv('MAIL_FROM_NAME') ?: $config['mail']['from_name'];
$config['mail']['debug_copy_to_storage'] = getenv('MAIL_DEBUG_STORAGE') !== false
    ? getenv('MAIL_DEBUG_STORAGE') !== '0'
    : (bool) $config['mail']['debug_copy_to_storage'];
$config['stripe']['secret_key'] = getenv('STRIPE_SECRET_KEY') ?: $config['stripe']['secret_key'];
$config['stripe']['publishable_key'] = getenv('STRIPE_PUBLISHABLE_KEY') ?: $config['stripe']['publishable_key'];
$config['stripe']['webhook_secret'] = getenv('STRIPE_WEBHOOK_SECRET') ?: $config['stripe']['webhook_secret'];

return $config;

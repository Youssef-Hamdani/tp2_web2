<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Csrf;
use App\Core\Session;

function config(string $key, mixed $default = null): mixed
{
    return App::config($key, $default);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = '/'): string
{
    $baseUrl = rtrim((string) config('app.base_url', ''), '/');

    if ($path === '/') {
        return $baseUrl !== '' ? $baseUrl . '/' : '/';
    }

    return $baseUrl . '/' . ltrim($path, '/');
}

function asset_url(string $path): string
{
    return url($path);
}

function redirect_back_default(string $fallback = '/'): string
{
    $returnUrl = Session::pull('_return_url');

    if (is_string($returnUrl) && $returnUrl !== '' && str_starts_with($returnUrl, '/')) {
        return $returnUrl;
    }

    return $fallback;
}

function old(string $key, mixed $default = ''): mixed
{
    $old = Session::get('_old_input', []);

    return is_array($old) ? ($old[$key] ?? $default) : $default;
}

function flash(string $type): ?string
{
    return Session::getFlash($type);
}

function csrf_token(string $formId): string
{
    return Csrf::token($formId);
}

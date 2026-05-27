<?php

declare(strict_types=1);

namespace App\Core;

final class Security
{
    public static function boot(): void
    {
        header_remove('x-powered-by');
        header('X-Frame-Options: DENY');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' https://js.stripe.com; frame-src https://js.stripe.com https://hooks.stripe.com; connect-src 'self' https://api.stripe.com; base-uri 'self'; form-action 'self'; frame-ancestors 'none';");

        if (self::shouldForceHttps() && !self::isHttpsRequest()) {
            $target = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');
            header('Location: ' . $target, true, 302);
            exit;
        }

        ini_set('session.use_strict_mode', '1');
        session_name((string) App::config('session.name', 'TP2Session'));

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => self::shouldSecureCookies(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        Session::start();
        Csrf::ensureSecret();
        Auth::refreshSession();
        Auth::attemptRememberLogin();
    }

    private static function shouldForceHttps(): bool
    {
        return (bool) App::config('app.force_https', false);
    }

    private static function shouldSecureCookies(): bool
    {
        return self::isHttpsRequest() || (bool) App::config('app.force_https', false);
    }

    public static function isHttpsRequest(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? '') === '443'
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function ensureSecret(): void
    {
        if (!Session::has('_csrf_secret')) {
            Session::put('_csrf_secret', bin2hex(random_bytes(32)));
        }
    }

    public static function token(string $formId): string
    {
        self::ensureSecret();
        $secret = (string) Session::get('_csrf_secret');

        return hash('sha256', $secret . '|' . $formId);
    }

    public static function verify(string $formId, ?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        return hash_equals(self::token($formId), $token);
    }

    public static function requireValid(string $formId, ?string $token, string $redirectTo): void
    {
        if (!self::verify($formId, $token)) {
            throw new ValidationException('Votre session a expiré. Veuillez réessayer.', $redirectTo);
        }
    }

    public static function rotateSecret(): void
    {
        Session::put('_csrf_secret', bin2hex(random_bytes(32)));
    }
}

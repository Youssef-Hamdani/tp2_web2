<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Repositories\UserRepository;

final class Auth
{
    public static function user(): ?User
    {
        $userId = Session::get('user_id');

        if (!is_int($userId) && !ctype_digit((string) $userId)) {
            return null;
        }

        return (new UserRepository())->findById((int) $userId);
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user?->id;
    }

    public static function check(): bool
    {
        return self::user() instanceof User;
    }

    public static function requireUser(string $returnUrl = '/connexion'): User
    {
        $user = self::user();

        if (!$user instanceof User) {
            Session::put('_return_url', $returnUrl);
            throw new ValidationException('Veuillez vous connecter pour continuer.', '/connexion');
        }

        return $user;
    }

    public static function login(User $user, bool $rememberMe = false): void
    {
        Session::start();
        session_regenerate_id(true);
        Session::put('user_id', $user->id);
        Session::put('user_role', $user->role);
        Session::put('_session_refresh_at', time() + (int) App::config('session.refresh_seconds', 900));
        Csrf::rotateSecret();

        if ($rememberMe) {
            $plainToken = bin2hex(random_bytes(32));
            $repository = new UserRepository();
            $expiresAt = date('Y-m-d H:i:s', time() + (int) App::config('auth.remember_for_seconds', 2592000));
            $repository->storeRememberToken($user->id, hash('sha256', $plainToken), $expiresAt);

            setcookie(
                (string) App::config('auth.remember_cookie', 'tp2_remember'),
                $user->id . '|' . $plainToken,
                [
                    'expires' => time() + (int) App::config('auth.remember_for_seconds', 2592000),
                    'path' => '/',
                    'secure' => Security::isHttpsRequest() || (bool) App::config('app.force_https', false),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]
            );
        }
    }

    public static function logout(): void
    {
        $userId = self::id();
        if ($userId !== null) {
            (new UserRepository())->clearRememberToken($userId);
        }

        setcookie((string) App::config('auth.remember_cookie', 'tp2_remember'), '', time() - 3600, '/');
        Session::destroy();
    }

    public static function attemptRememberLogin(): void
    {
        if (self::check()) {
            return;
        }

        $cookieName = (string) App::config('auth.remember_cookie', 'tp2_remember');
        $cookie = $_COOKIE[$cookieName] ?? '';

        if (!is_string($cookie) || !str_contains($cookie, '|')) {
            return;
        }

        [$userId, $plainToken] = explode('|', $cookie, 2);

        if (!ctype_digit($userId) || $plainToken === '') {
            return;
        }

        $user = (new UserRepository())->findForRememberToken((int) $userId, hash('sha256', $plainToken));

        if (!$user instanceof User) {
            return;
        }

        self::login($user, true);
    }

    public static function refreshSession(): void
    {
        $refreshAt = Session::get('_session_refresh_at');

        if (!self::check() || !is_int($refreshAt)) {
            return;
        }

        if (time() >= $refreshAt) {
            session_regenerate_id(true);
            Session::put('_session_refresh_at', time() + (int) App::config('session.refresh_seconds', 900));
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        if ($uri === null || $uri === '') {
            return '/';
        }

        $trimmed = rtrim($uri, '/');

        return $trimmed !== '' ? $trimmed : '/';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return isset($_FILES[$key]) && is_array($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public function only(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->input($key);
        }

        return $values;
    }

    public function storeOldInput(array $input): void
    {
        Session::put('_old_input', $input);
    }

    public function clearOldInput(): void
    {
        Session::forget('_old_input');
    }
}

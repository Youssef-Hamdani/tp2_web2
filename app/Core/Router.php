<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>\d+)', $path);
        $normalized = rtrim((string) $pattern, '/');
        $pattern = '#^' . ($normalized === '' ? '/' : $normalized) . '$#';
        $this->routes[] = compact('method', 'path', 'pattern', 'handler');
    }

    public function dispatch(): void
    {
        $request = new Request();
        $method = $request->method();
        $path = $request->path();

        try {
            foreach ($this->routes as $route) {
                if ($route['method'] !== $method) {
                    continue;
                }

                if (preg_match($route['pattern'], $path, $matches) !== 1) {
                    continue;
                }

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                $params = array_values(array_filter($matches, static fn (string|int $key): bool => !is_int($key), ARRAY_FILTER_USE_KEY));

                call_user_func_array([$controller, $action], $params);
                return;
            }

            http_response_code(404);
            View::render('errors/404');
        } catch (ValidationException $exception) {
            Session::put('_flash_error', $exception->getMessage());
            Response::redirect($exception->getRedirectTo());
        } catch (Throwable $throwable) {
            Logger::error('Unhandled exception', [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
            ]);

            http_response_code(500);
            if ((bool) App::config('app.debug', false)) {
                echo nl2br(e($throwable->getMessage() . "\n" . $throwable->getTraceAsString()));
                return;
            }

            View::render('errors/500');
        }
    }
}

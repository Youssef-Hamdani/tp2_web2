<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseController
{
    protected Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? new Request();
    }

    protected function view(string $template, array $data = []): void
    {
        View::render($template, $data);
    }

    protected function redirect(string $path): never
    {
        Response::redirect($path);
    }

    protected function success(string $message): void
    {
        Session::put('_flash_success', $message);
    }

    protected function error(string $message): void
    {
        Session::put('_flash_error', $message);
    }

    protected function validateCsrf(string $formId, string $redirectTo): void
    {
        Csrf::requireValid($formId, (string) $this->request->post('_csrf'), $redirectTo);
    }
}

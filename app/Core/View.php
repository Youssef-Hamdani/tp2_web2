<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $templateFile = __DIR__ . '/../Views/' . $template . '.php';
        $layoutFile = __DIR__ . '/../Views/layouts/' . $layout . '.php';

        if (!is_file($templateFile) || !is_file($layoutFile)) {
            http_response_code(500);
            echo 'Vue introuvable.';
            return;
        }

        $auth = Auth::user();
        $request = new Request();
        $currentPath = $request->path();
        $successMessage = Session::pull('_flash_success');
        $errorMessage = Session::pull('_flash_error');

        extract($data, EXTR_SKIP);

        ob_start();
        require $templateFile;
        $content = (string) ob_get_clean();

        require $layoutFile;
    }
}

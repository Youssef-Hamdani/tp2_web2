<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    public function __construct(string $message, private readonly string $redirectTo = '/')
    {
        parent::__construct($message);
    }

    public function getRedirectTo(): string
    {
        return $this->redirectTo;
    }
}

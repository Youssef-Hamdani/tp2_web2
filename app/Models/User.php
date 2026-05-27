<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $passwordHash,
        public readonly string $role,
        public readonly bool $isActive,
        public readonly ?string $activationTokenHash,
        public readonly ?string $activationExpiresAt,
        public readonly ?string $resetTokenHash,
        public readonly ?string $resetExpiresAt,
        public readonly ?string $rememberTokenHash,
        public readonly ?string $rememberExpiresAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }
}

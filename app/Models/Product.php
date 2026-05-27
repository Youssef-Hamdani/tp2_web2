<?php

declare(strict_types=1);

namespace App\Models;

final class Product
{
    public function __construct(
        public readonly int $id,
        public readonly int $sellerUserId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $imagePath,
        public readonly int $priceCents,
        public readonly int $serviceFeeCents,
        public readonly string $status,
        public readonly ?string $soldAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly ?string $sellerEmail = null,
    ) {
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}

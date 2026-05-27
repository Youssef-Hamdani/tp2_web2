<?php

declare(strict_types=1);

namespace App\Models;

final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly int $buyerUserId,
        public readonly int $sellerUserId,
        public readonly int $productId,
        public readonly string $productName,
        public readonly int $subtotalCents,
        public readonly int $serviceFeeCents,
        public readonly int $gstCents,
        public readonly int $qstCents,
        public readonly int $totalCents,
        public readonly string $buyerFirstName,
        public readonly string $buyerLastName,
        public readonly string $billingAddressLine1,
        public readonly ?string $billingAddressLine2,
        public readonly string $billingCity,
        public readonly string $billingProvince,
        public readonly string $billingPostalCode,
        public readonly string $shippingAddressLine1,
        public readonly ?string $shippingAddressLine2,
        public readonly string $shippingCity,
        public readonly string $shippingProvince,
        public readonly string $shippingPostalCode,
        public readonly ?string $stripeSessionId,
        public readonly ?string $stripePaymentIntentId,
        public readonly string $stripeStatus,
        public readonly string $status,
        public readonly ?string $paymentPayload,
        public readonly ?string $purchasedAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }
}

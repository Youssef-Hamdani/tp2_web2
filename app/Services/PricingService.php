<?php

declare(strict_types=1);

namespace App\Services;

final class PricingService
{
    public function serviceFeeCents(int $priceCents): int
    {
        $percent = (float) config('pricing.stripe_percent', 0.029);
        $fixed = (int) config('pricing.stripe_fixed_cents', 30);
        $minimum = (int) config('pricing.minimum_fee_cents', 45);

        return max($minimum, (int) ceil($priceCents * $percent) + $fixed);
    }

    public function taxes(int $subtotalCents): array
    {
        $gst = (int) round($subtotalCents * (float) config('pricing.gst_rate', 0.05));
        $qst = (int) round($subtotalCents * (float) config('pricing.qst_rate', 0.09975));

        return [
            'gst_cents' => $gst,
            'qst_cents' => $qst,
        ];
    }

    public function summary(int $subtotalCents): array
    {
        $serviceFee = $this->serviceFeeCents($subtotalCents);
        $taxes = $this->taxes($subtotalCents);
        $total = $subtotalCents + $serviceFee + $taxes['gst_cents'] + $taxes['qst_cents'];

        return [
            'subtotal_cents' => $subtotalCents,
            'service_fee_cents' => $serviceFee,
            'gst_cents' => $taxes['gst_cents'],
            'qst_cents' => $taxes['qst_cents'],
            'total_cents' => $total,
        ];
    }

    public function formatCents(int $amount): string
    {
        return number_format($amount / 100, 2, ',', ' ') . ' $';
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\PricingService;
use Tests\Support\TestCase;

final class PricingServiceTest extends TestCase
{
    public function testCalculeLeFraisMinimal(): void
    {
        $service = new PricingService();
        $this->assertSame(45, $service->serviceFeeCents(1));
    }

    public function testCalculeLesTaxesDuQuebec(): void
    {
        $service = new PricingService();
        $taxes = $service->taxes(10000);

        $this->assertSame(500, $taxes['gst_cents']);
        $this->assertSame(998, $taxes['qst_cents']);
    }
}

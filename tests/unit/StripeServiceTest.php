<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\StripeService;
use Tests\Support\TestCase;

final class StripeServiceTest extends TestCase
{
    public function testDetecteUneConfigurationStripeAbsente(): void
    {
        $service = new StripeService();
        $this->assertFalse($service->isConfigured());
    }
}

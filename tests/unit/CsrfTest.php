<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Csrf;
use Tests\Support\TestCase;

final class CsrfTest extends TestCase
{
    public function testCreeUnJetonValide(): void
    {
        $token = Csrf::token('form-test');
        $this->assertTrue(Csrf::verify('form-test', $token));
    }

    public function testRefuseUnJetonInvalide(): void
    {
        $this->assertFalse(Csrf::verify('form-test', 'invalide'));
    }
}

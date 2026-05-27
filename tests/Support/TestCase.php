<?php

declare(strict_types=1);

namespace Tests\Support;

abstract class TestCase
{
    private int $assertions = 0;

    protected function assertTrue(bool $condition, string $message = 'Assertion true échouée.'): void
    {
        $this->assertions++;
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    protected function assertFalse(bool $condition, string $message = 'Assertion false échouée.'): void
    {
        $this->assertTrue(!$condition, $message);
    }

    protected function assertSame(mixed $expected, mixed $actual, string $message = 'Assertion same échouée.'): void
    {
        $this->assertions++;
        if ($expected !== $actual) {
            throw new \RuntimeException($message . " Attendu: " . var_export($expected, true) . " Reçu: " . var_export($actual, true));
        }
    }

    protected function assertContains(string $needle, string $haystack, string $message = 'Assertion contains échouée.'): void
    {
        $this->assertions++;
        if (!str_contains($haystack, $needle)) {
            throw new \RuntimeException($message);
        }
    }

    public function assertionsCount(): int
    {
        return $this->assertions;
    }
}

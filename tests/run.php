<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$files = glob(__DIR__ . '/unit/*Test.php');
$totalAssertions = 0;
$totalTests = 0;

foreach ($files as $file) {
    require_once $file;
}

$classes = array_filter(
    get_declared_classes(),
    static fn (string $class): bool => str_starts_with($class, 'Tests\\Unit\\')
);

foreach ($classes as $class) {
    $test = new $class();
    $methods = array_filter(get_class_methods($test), static fn (string $method): bool => str_starts_with($method, 'test'));

    foreach ($methods as $method) {
        $before = $test->assertionsCount();
        $test->$method();
        $totalTests++;
        $totalAssertions += $test->assertionsCount() - $before;
    }
}

echo "Tests réussis: {$totalTests}\n";
echo "Assertions: {$totalAssertions}\n";

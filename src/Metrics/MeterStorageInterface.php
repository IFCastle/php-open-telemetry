<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry\Metrics;

interface MeterStorageInterface
{
    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     */
    public function record(MeterInterface $meter, mixed $value, iterable $attributes = []): void;
}

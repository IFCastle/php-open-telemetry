<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry\Metrics\Nope;

use IfCastle\OpenTelemetry\Metrics\MeterInterface;
use IfCastle\OpenTelemetry\Metrics\MeterStorageInterface;

final readonly class NopeStorage implements MeterStorageInterface
{
    #[\Override]
    public function record(MeterInterface $meter, mixed $value, iterable $attributes = []): void {}
}

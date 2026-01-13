<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface SpanLoggerInterface
{
    public function defineSpanKind(SpanKindEnum $spanKind): static;

    /**
     * @param iterable<string, scalar|null> $attributes
     *
     */
    public function startSpan(string $spanName, iterable $attributes = []): SpanInterface;

    public function endSpan(?SpanInterface $span = null): void;
}

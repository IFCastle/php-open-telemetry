<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface SpanLoggerInterface
{
    public function defineSpanKind(SpanKindEnum $spanKind): static;

    /**
     * @param string $spanName
     * @param iterable<string, scalar|null> $attributes
     *
     * @return SpanInterface
     */
    public function startSpan(string $spanName, iterable $attributes = []): SpanInterface;

    public function endSpan(?SpanInterface $span = null): void;
}

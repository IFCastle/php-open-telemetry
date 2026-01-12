<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface TracerInterface extends TelemetryLoggerInterface
{
    public function getResource(): ResourceInterface;

    public function newTelemetryContext(): TelemetryContextInterface;

    public function createTrace(): TraceInterface;

    public function endTrace(TraceInterface $trace): void;

    /**
     * @param string $spanName
     * @param SpanKindEnum $spanKind
     * @param InstrumentationScopeInterface|null $instrumentationScope
     * @param iterable<string, scalar|null> $attributes
     *
     * @return SpanInterface
     */
    public function createSpan(
        string                        $spanName,
        SpanKindEnum                  $spanKind,
        ?InstrumentationScopeInterface $instrumentationScope = null,
        iterable                      $attributes           = []
    ): SpanInterface;

    public function endSpan(?SpanInterface $span = null): void;

    /**
     * @param InstrumentationScopeInterface $instrumentationScope
     * @param string $level
     * @param array<scalar|object|scalar[]>|string|bool|int|float|null $body
     * @param iterable<string, scalar|null> $attributes
     */
    public function registerLog(
        InstrumentationScopeInterface $instrumentationScope,
        string $level,
        array|string|bool|int|float|null $body,
        iterable $attributes = []
    ): void;

    public function cleanTelemetry(): void;
}

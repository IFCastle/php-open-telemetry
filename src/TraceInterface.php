<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface TraceInterface
{
    public function newSpanId(): string;

    public function getTraceId(): string;

    public function isExternal(): bool;

    public function getCurrentSpanId(): ?string;

    public function getCurrentSpan(): ?SpanInterface;

    public function getParentSpan(): ?SpanInterface;

    public function getResource(): ResourceInterface;

    public function setResource(ResourceInterface $resource): static;

    public function findInstrumentationScopeId(InstrumentationScopeInterface $instrumentationScope): string;

    /**
     * @param iterable<string, scalar|null> $attributes
     *
     */
    public function createSpan(
        string                        $spanName,
        SpanKindEnum                  $spanKind,
        ?InstrumentationScopeInterface $instrumentationScope = null,
        iterable                      $attributes = []
    ): SpanInterface;

    public function endSpan(?SpanInterface $span = null): void;

    /**
     * @return array<string, InstrumentationScopeInterface>
     */
    public function getInstrumentationScopes(): array;

    /**
     * @return array<string, SpanInterface[]>
     */
    public function getSpansByInstrumentationScope(): array;

    public function end(): void;

    public function cleanSpans(): void;
}

<?php
declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface TracerInterface extends TelemetryLoggerInterface
{
    public function getResource(): ResourceInterface;
    
    public function newTelemetryContext(): TelemetryContextInterface;
    
    public function createTrace(): TraceInterface;
    
    public function endTrace(TraceInterface $trace): void;
    
    public function createSpan(
        string                        $spanName,
        SpanKindEnum                  $spanKind,
        InstrumentationScopeInterface $instrumentationScope = null,
        array                         $attributes           = []
    ): SpanInterface;
    
    public function endSpan(SpanInterface $span = null): void;
    
    public function registerLog(
        InstrumentationScopeInterface $instrumentationScope,
        string $level,
        array|string|bool|int|float|null $body,
        iterable $attributes = []
    ): void;
    
    public function cleanTelemetry(): void;
}
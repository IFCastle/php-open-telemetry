<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface TraceExporterInterface
{
    /**
     * @param array<InstrumentationScopeInterface> $instrumentationScopes
     * @param array<string, SpanInterface[]> $spansByScope
     */
    public function exportTraces(ResourceInterface $resource, array $instrumentationScopes, array $spansByScope): void;

    /**
     * @param array<InstrumentationScopeInterface> $instrumentationScopes
     * @param array<string, Log[]> $logsByScope
     */
    public function exportLogs(ResourceInterface $resource, array $instrumentationScopes, array $logsByScope): void;

    public function deferredSendRawTelemetry(string $endpoint, string $payload): void;
}

<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface TelemetryContextInterface
{
    public function getTraceId(): string|null;

    public function getSpanId(): string|null;

    public function getCurrentTrace(): ?TraceInterface;

    public function getTraceFlags(): TraceFlagsEnum;

    public function end(): void;
}

<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface TelemetryLoggerAwareInterface
{
    public function getTelemetryLogger(): TelemetryLoggerInterface;
}

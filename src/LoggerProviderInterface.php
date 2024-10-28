<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface LoggerProviderInterface
{
    public function provideLogger(InstrumentationScopeInterface $instrumentationScope): TelemetryLoggerInterface;
}

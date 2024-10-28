<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface ExceptionFormatterInterface
{
    /**
     * @param \Throwable $throwable
     * @return array<string, scalar|null>
     */
    public function buildExceptionAttributes(\Throwable $throwable): array;
}

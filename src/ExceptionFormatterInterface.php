<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface ExceptionFormatterInterface
{
    public function getSeverityText(\Throwable $throwable): string;

    /**
     * @return array<string, scalar|scalar[]|null>|string
     */
    public function buildExceptionReport(\Throwable $throwable): array|string;

    /**
     * @param iterable<string, scalar|null> $attributes
     *
     * @return array<string, scalar|null>
     */
    public function buildExceptionAttributes(\Throwable $throwable, iterable $attributes = []): array;
}

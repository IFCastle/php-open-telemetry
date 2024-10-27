<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use Psr\Log\LoggerInterface;

/**
 * OpenTelemetry compatible logger interface.
 * + Psr3 compatible logger interface.
 */
interface TelemetryLoggerInterface extends LoggerInterface
{
    /**
     * Record event with name, attributes, and timestamp.
     *
     * @param   string                          $name           Event name.
     * @param   iterable<string, scalar|null>   $attributes     Key-value pairs of event attributes.
     * @param   int|null                        $timestamp      Unix timestamp in nanoseconds.
     */
    public function addEvent(string $name, iterable $attributes = [], ?int $timestamp = null): void;

    /**
     * Add exception to the log.
     *
     * @param   \Throwable                      $throwable  Exception to record.
     * @param   iterable<string, scalar|null>   $attributes Key-value pairs of exception attributes.
     */
    public function recordException(\Throwable $throwable, iterable $attributes = []): void;
}

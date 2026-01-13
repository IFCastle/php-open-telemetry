<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use Psr\Log\LogLevel;

/**
 * This model does not include the observed_time_unix_nano
 * field because it is unnecessary in most cases.
 *
 * @see https://opentelemetry.io/docs/specs/otel/logs/data-model/
 */
readonly class Log
{
    /**
     * @param float|array<scalar|scalar[]|null>|bool|int|string|null $body
     * @param array<string, scalar|null> $attributes
     */
    public function __construct(
        /**
         * $timeUnixNano is the time when the event occurred.
         * Value is UNIX Epoch time in nanoseconds since 00:00:00 UTC on 1 January 1970.
         * The Value of 0 indicates unknown or missing timestamp.
         */
        public int                  $timeUnixNano,
        public string               $level,
        public float|array|bool|int|string|null $body,
        public array                $attributes,
        public ?string              $traceId,
        public ?string              $spanId,
        public ?TraceFlagsEnum      $flags,
    ) {}

    public function getSeverityNumber(): int
    {
        return $this->errorLevelToSeverityNumber($this->level);
    }

    protected function errorLevelToSeverityNumber(string $level): int
    {
        // According to OpenTelemetry specification
        // @see https://opentelemetry.io/docs/specs/otel/logs/data-model/#field-severitynumber
        return match ($level) {
            LogLevel::EMERGENCY     => 23,
            LogLevel::ALERT         => 21,
            LogLevel::CRITICAL      => 20,
            LogLevel::ERROR         => 17,
            LogLevel::WARNING       => 14,
            LogLevel::NOTICE        => 13,
            LogLevel::INFO          => 9,
            LogLevel::DEBUG         => 1,
            default                 => 0
        };
    }
}

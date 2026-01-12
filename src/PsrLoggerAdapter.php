<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

final readonly class PsrLoggerAdapter implements TelemetryLoggerInterface
{
    use LoggerTrait;

    public function __construct(private LoggerInterface $logger) {}


    #[\Override]
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    public function addEvent(string $name, iterable $attributes = [], ?int $timestamp = null): void
    {
        $attributes = \iterator_to_array($attributes);

        if ($timestamp !== null) {
            $attributes['timestamp'] = $timestamp;
        }

        $this->logger->log(LogLevel::INFO, $name, $attributes);
    }

    public function recordException(\Throwable $throwable, iterable $attributes = []): void
    {
        $attributes                 = \iterator_to_array($attributes);
        // Put an exception object into the attribute array according to the PS3 specification.
        $attributes['exception']    = $throwable;

        $this->logger->log(LogLevel::ERROR, $throwable->getMessage(), $attributes);
    }
}

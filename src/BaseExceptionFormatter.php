<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use IfCastle\Exceptions\BaseExceptionInterface;
use Psr\Log\LogLevel;

final readonly class BaseExceptionFormatter implements ExceptionFormatterInterface
{
    public function __construct(private ExceptionFormatterInterface $exceptionFormatter = new ExceptionFormatter()) {}

    #[\Override]
    public function getSeverityText(\Throwable $throwable): string
    {
        if ($throwable instanceof BaseExceptionInterface && $throwable->isFatal()) {
            return LogLevel::CRITICAL;
        }

        return $this->exceptionFormatter->getSeverityText($throwable);
    }

    #[\Override]
    public function buildExceptionReport(\Throwable $throwable): array|string
    {
        if (false === $throwable instanceof BaseExceptionInterface) {
            return $this->exceptionFormatter->buildExceptionReport($throwable);
        }

        return $throwable->toArray();
    }

    #[\Override]
    public function buildExceptionAttributes(\Throwable $throwable, iterable $attributes = []): array
    {
        if (!\is_array($attributes)) {
            $attributes             = \iterator_to_array($attributes);
        }

        if (false === $throwable instanceof BaseExceptionInterface) {
            return $this->exceptionFormatter->buildExceptionAttributes($throwable, $attributes);
        }

        $attributes                 = $this->exceptionFormatter->buildExceptionAttributes($throwable, $attributes);

        $attributes['tags']         = \implode(', ', $throwable->getTags());
        $attributes['exception.template'] = $throwable->getTemplate();

        return $attributes;
    }
}

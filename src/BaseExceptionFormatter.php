<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use IfCastle\Exceptions\BaseExceptionInterface;

final readonly class BaseExceptionFormatter implements ExceptionFormatterInterface
{
    public function __construct(private ExceptionFormatterInterface $exceptionFormatter = new ExceptionFormatter()) {}


    #[\Override]
    public function buildExceptionAttributes(\Throwable $throwable): array
    {
        if (false === $throwable instanceof BaseExceptionInterface) {
            return $this->exceptionFormatter->buildExceptionAttributes($throwable);
        }

        $attributes                 = $this->exceptionFormatter->buildExceptionAttributes($throwable);

        $attributes['tags']         = \implode(', ', $throwable->getTags());
        $attributes['exception.template'] = $throwable->getTemplate();

        return $attributes;
    }
}

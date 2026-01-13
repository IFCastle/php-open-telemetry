<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

class Event implements ElementInterface, AttributesInterface, TimestampInterface
{
    use ElementTrait;
    use AttributesTrait;
    use TimestampTrait;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     * @param int|null $timestamp   Unix nano timestamp
     */
    public function __construct(string $name, iterable $attributes = [], ?int $timestamp = null)
    {
        $this->name                 = $name;
        $this->attributes           = $attributes;
        $this->timestamp            = $timestamp ?? SystemClock::now();
    }
}

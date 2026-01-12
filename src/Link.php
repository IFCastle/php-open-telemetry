<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

class Link implements LinkInterface
{
    use ElementTrait;
    use AttributesTrait;
    use SpanElementTrait;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     */
    public function __construct(
        string $traceId,
        string $spanId,
        iterable $attributes   = []
    ) {
        if (\is_array($attributes)) {
            $attributes = \iterator_to_array($attributes);
        }

        $this->traceId      = $traceId;
        $this->spanId       = $spanId;
        $this->attributes   = $attributes;
    }
}

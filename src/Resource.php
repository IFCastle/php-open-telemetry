<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

class Resource implements ResourceInterface
{
    use ElementTrait;
    use AttributesTrait;

    /**
     * @param array<string, scalar|null> $attributes
     */
    public function __construct(
        string $name,
        array $attributes       = [],
        string $schemaUrl       = '',
    ) {
        $this->name             = $name;
        $this->attributes       = $attributes;
        $this->schemaUrl        = $schemaUrl;
    }
}

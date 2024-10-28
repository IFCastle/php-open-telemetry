<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

final class TelemetryAttributes implements AttributesInterface
{
    use AttributesTrait;

    /**
     * @param array<string, scalar|null> $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }
}

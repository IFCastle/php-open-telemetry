<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

#[\Attribute(\Attribute::TARGET_CLASS)]
class InstrumentationScope implements InstrumentationScopeInterface
{
    use ElementTrait;
    use AttributesTrait;

    public function __construct(
        string      $name,
        protected ?string     $version    = null,
        string      $schemaUrl  = '',
        array       $attributes = []
    ) {
        $this->name                 = $name;
        $this->schemaUrl            = $schemaUrl;
        $this->attributes           = $attributes;
    }

    #[\Override]
    public function getVersion(): ?string
    {
        return $this->version;
    }
}

<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry\Metrics;

use IfCastle\OpenTelemetry\AttributesTrait;
use IfCastle\OpenTelemetry\ElementTrait;
use IfCastle\OpenTelemetry\InstrumentationScopeInterface;

abstract class MeterAbstract implements MeterInterface
{
    use ElementTrait;
    use AttributesTrait;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     */
    public function __construct(
        protected MeterStorageInterface $storage,
        protected InstrumentationScopeInterface $instrumentationScope,
        string $name,
        protected ?string $unit                 = null,
        protected ?string $description          = null,
        iterable $attributes                    = []
    ) {
        $this->name                 = $name;
        $this->attributes           = $attributes;
    }

    #[\Override]
    public function getMeterId(): string
    {
        return 'm' . \spl_object_id($this);
    }

    #[\Override]
    public function add(mixed $value, iterable $attributes = []): void
    {
        $this->storage->record($this, $value, $attributes);
    }
}

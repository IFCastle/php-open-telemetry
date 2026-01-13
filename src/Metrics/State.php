<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry\Metrics;

use IfCastle\OpenTelemetry\InstrumentationScopeInterface;

class State extends MeterAbstract implements StateInterface
{
    /**
     * @param array<string, scalar|null>    $attributes
     */
    public function __construct(
        MeterStorageInterface         $storage,
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $unit = null,
        ?string                       $description = null,
        array                         $attributes = []
    ) {
        parent::__construct($storage, $instrumentationScope, $name, $unit, $description, $attributes);
    }

    #[\Override]
    public function setStateOk(string $message = ''): void
    {
        $this->add(0);
    }

    #[\Override]
    public function setStateError(string $message = ''): void
    {
        $this->add(1);
    }
}

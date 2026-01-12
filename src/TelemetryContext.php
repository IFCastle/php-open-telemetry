<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

final class TelemetryContext implements TelemetryContextInterface
{
    protected ?TraceInterface $trace        = null;

    /**
     * @var \WeakReference<TracerInterface>|null
     */
    protected ?\WeakReference $tracer       = null;

    public function __construct(TracerInterface $tracer)
    {
        $this->tracer                       = \WeakReference::create($tracer);
        $this->trace                        = $tracer->createTrace();
    }

    #[\Override]
    public function getCurrentTrace(): ?TraceInterface
    {
        return $this->trace;
    }

    #[\Override]
    public function getTraceId(): ?string
    {
        return $this->trace?->getTraceId();
    }

    #[\Override]
    public function getSpanId(): ?string
    {
        return $this->trace?->getCurrentSpanId();
    }

    #[\Override]
    public function getTraceFlags(): TraceFlagsEnum
    {
        return TraceFlagsEnum::SAMPLED;
    }

    #[\Override]
    public function end(): void
    {
        $this->trace?->end();

        if ($this->trace !== null) {
            $this->tracer?->get()?->endTrace($this->trace);
        }
    }
}

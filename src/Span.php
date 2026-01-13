<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use Psr\Log\LoggerTrait;

class Span implements SpanInterface
{
    use ElementTrait;
    use AttributesTrait;
    use SpanElementTrait;
    use LoggerTrait;

    /**
     * @var \WeakReference<TraceInterface>|null
     */
    protected ?\WeakReference $trace = null;

    protected SpanKindEnum $kind     = SpanKindEnum::INTERNAL;

    protected int $startTime         = 0;

    protected int $endTime           = 0;

    protected StatusCodeEnum $status = StatusCodeEnum::STATUS_UNSET;

    protected string $statusDescription = '';

    protected bool   $hasEnded        = false;

    /**
     * @var array<Event>
     */
    protected array  $events         = [];

    /**
     * @var array<LinkInterface>
     */
    protected array $links           = [];

    protected TraceState $traceState;

    /**
     * @param array<string, scalar|null>     $attributes
     */
    public function __construct(
        TraceInterface $trace,
        string $name,
        ?SpanKindEnum $kind          = null,
        array $attributes           = [],
        protected ?InstrumentationScopeInterface $instrumentationScope = null,
        protected ExceptionFormatterInterface $exceptionFormatter = new ExceptionFormatter()
    ) {
        $this->trace                = \WeakReference::create($trace);
        $this->traceId              = $trace->getTraceId();
        $this->spanId               = $trace->newSpanId();
        $this->name                 = $name;
        $this->kind                 = $kind ?? SpanKindEnum::INTERNAL;
        $this->attributes           = $attributes;
        $this->traceState           = new TraceState();

        $this->startTime            = SystemClock::now();
    }

    /**
     * PSR-3 log adapter method.
     * Translates PSR-3 log messages into OpenTelemetry Span-events.
     *
     * @param array<string,scalar|object|scalar[]> $context
     *
     */
    #[\Override]
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if ($context['exception'] instanceof \Throwable) {
            $this->recordException($context['exception'], $context);
            return;
        }

        if (\array_key_exists('severity', $context)) {
            $context['severity']    = $level;
        }

        $this->addEvent($message, $context);
    }

    protected function getTrace(): ?TraceInterface
    {
        return $this->trace?->get();
    }

    #[\Override]
    public function getParentSpanId(): ?string
    {
        return $this->getTrace()?->getParentSpan()?->getSpanId();
    }

    #[\Override]
    public function getTraceFlags(): TraceFlagsEnum
    {
        return TraceFlagsEnum::DEFAULT;
    }

    #[\Override]
    public function getSpanName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function getSpanKind(): SpanKindEnum
    {
        return $this->kind;
    }

    #[\Override]
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    #[\Override]
    public function getTimeUnixNano(): int
    {
        return $this->startTime;
    }

    #[\Override]
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    #[\Override]
    public function getDuration(): int
    {
        return (int) \ceil($this->getDurationNanos() / 1000000000);
    }

    #[\Override]
    public function getDurationNanos(): int
    {
        return $this->endTime - $this->startTime;
    }

    #[\Override]
    public function getTraceState(): TraceState
    {
        return $this->traceState;
    }

    #[\Override]
    public function getEvents(): array
    {
        return $this->events;
    }

    #[\Override]
    public function addEvent(string $name, iterable $attributes = [], ?int $timestamp = null): void
    {
        if ($this->hasEnded) {
            return;
        }

        $this->events[]             = new Event($name, $attributes, $timestamp);
    }

    #[\Override]
    public function recordException(\Throwable $throwable, iterable $attributes = []): void
    {
        if ($this->hasEnded) {
            return;
        }

        // Automatically set status to ERROR
        $this->status               = StatusCodeEnum::STATUS_ERROR;

        if (!\is_array($attributes)) {
            $attributes             = \iterator_to_array($attributes);
        }

        if ($attributes === []) {
            $attributes             = $this->exceptionFormatter->buildExceptionAttributes($throwable, $attributes);
        }

        $this->events[]             = new Event('exception', $attributes, SystemClock::now());
    }

    #[\Override]
    public function getStatus(): StatusCodeEnum
    {
        return $this->status;
    }

    #[\Override]
    public function getStatusDescription(): string
    {
        return $this->statusDescription;
    }

    #[\Override]
    public function setStatus(StatusCodeEnum $status, string $description = ''): static
    {
        if ($this->hasEnded) {
            return $this;
        }

        $this->status               = $status;

        return $this;
    }

    #[\Override]
    public function isRecording(): bool
    {
        return false === $this->hasEnded;
    }

    #[\Override]
    public function hasEnded(): bool
    {
        return $this->hasEnded;
    }

    #[\Override]
    public function end(?int $endEpochNanos = null): void
    {
        if ($this->hasEnded) {
            return;
        }

        $this->endTime              = $endEpochNanos ?? SystemClock::now();
        $this->hasEnded             = true;

        if ($this->status === StatusCodeEnum::STATUS_UNSET) {
            $this->status           = StatusCodeEnum::STATUS_OK;
        }
    }

    #[\Override]
    public function getLinks(): array
    {
        return $this->links;
    }

    #[\Override]
    public function addLink(LinkInterface $link): static
    {
        if ($this->hasEnded) {
            return $this;
        }

        $this->links[]              = $link;

        return $this;
    }

    #[\Override]
    public function getInstrumentationScope(): ?InstrumentationScopeInterface
    {
        return $this->instrumentationScope;
    }
}

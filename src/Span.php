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

    protected ?\WeakReference $trace = null;
    protected SpanKindEnum $kind     = SpanKindEnum::INTERNAL;
    protected int $startTime         = 0;
    protected int $endTime           = 0;
    protected ?InstrumentationScopeInterface $instrumentationScope = null;
    protected StatusCodeEnum $status = StatusCodeEnum::STATUS_UNSET;
    protected string $statusDescription = '';
    protected bool   $hasEnded        = false;
    protected array  $events          = [];
    protected array $links           = [];
    protected TraceState $traceState;
    protected ExceptionFormatterInterface|null $exceptionFormatter = null;

    public function __construct(
        TraceInterface $trace,
        string $name,
        ?SpanKindEnum $kind          = null,
        array $attributes           = [],
        ?InstrumentationScopeInterface $instrumentationScope = null,
        ?ExceptionFormatterInterface $exceptionFormatter = null
    ) {
        $this->trace                = \WeakReference::create($trace);
        $this->traceId              = $trace->getTraceId();
        $this->spanId               = $trace->newSpanId();
        $this->name                 = $name;
        $this->kind                 = $kind ?? SpanKindEnum::INTERNAL;
        $this->attributes           = $attributes;
        $this->instrumentationScope = $instrumentationScope;
        $this->exceptionFormatter   = $exceptionFormatter ?? new ExceptionFormatter();
        $this->traceState           = new TraceState();

        $this->startTime            = SystemClock::now();
    }

    /**
     * PSR-3 log adapter method.
     * Translates PSR-3 log messages into OpenTelemetry Span-events.
     *
     * @param array<string,scalar|scalar[]> $context
     *
     */
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

    public function getParentSpanId(): ?string
    {
        return $this->getTrace()?->getParentSpan()?->getSpanId();
    }

    public function getTraceFlags(): TraceFlagsEnum
    {
        return TraceFlagsEnum::DEFAULT;
    }

    public function getSpanName(): string
    {
        return $this->name;
    }

    public function getSpanKind(): SpanKindEnum
    {
        return $this->kind;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getTimeUnixNano(): int
    {
        return $this->startTime;
    }

    public function getEndTime(): int
    {
        return $this->endTime;
    }

    public function getDuration(): int
    {
        return (int) \ceil($this->getDurationNanos() / 1000000000);
    }

    public function getDurationNanos(): int
    {
        return $this->endTime - $this->startTime;
    }

    public function getTraceState(): TraceState
    {
        return $this->traceState;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function addEvent(string $name, iterable $attributes = [], ?int $timestamp = null): void
    {
        if ($this->hasEnded) {
            return;
        }

        $this->events[]             = new Event($name, $attributes, $timestamp);
    }

    public function recordException(\Throwable $throwable, iterable $attributes = []): void
    {
        if ($this->hasEnded) {
            return;
        }

        // Automatically set status to ERROR
        $this->status               = StatusCodeEnum::STATUS_ERROR;

        $attributes                 = \iterator_to_array($attributes);

        if ($attributes === []) {
            $attributes             = ExceptionFormatter::buildAttributes($throwable);
        }

        $this->events[]             = new Event('exception', $attributes, SystemClock::now());
    }

    public function getStatus(): StatusCodeEnum
    {
        return $this->status;
    }

    public function getStatusDescription(): string
    {
        return $this->statusDescription;
    }

    public function setStatus(StatusCodeEnum $status, string $description = ''): static
    {
        if ($this->hasEnded) {
            return $this;
        }

        $this->status               = $status;

        return $this;
    }

    public function isRecording(): bool
    {
        return false === $this->hasEnded;
    }

    public function hasEnded(): bool
    {
        return $this->hasEnded;
    }

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

    public function getLinks(): array
    {
        return $this->links;
    }

    public function addLink(LinkInterface $link): static
    {
        if ($this->hasEnded) {
            return $this;
        }

        $this->links[]              = $link;

        return $this;
    }

    public function getInstrumentationScope(): ?InstrumentationScopeInterface
    {
        return $this->instrumentationScope;
    }
}

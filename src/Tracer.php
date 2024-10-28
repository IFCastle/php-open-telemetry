<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Tracer implements TracerInterface
{
    use LoggerTrait;

    /**
     * If true, all logs will be sent as SpanEvents.
     * (need for Jaeger. because they don't support the OpenTelemetry Log concept).
     */
    protected bool $populateLogsAsSpanEvents = false;

    /**
     * If true, all exceptions will be sent as Log.
     */
    protected bool $populateExceptionsToLog = false;

    /**
     * If true, all exceptions will be sent as Span.
     */
    protected bool $populateExceptionsToSpan = false;

    /**
     * If true, all logs will be copied to PSR-3 logger.
     */
    protected bool $copyLogsToPsrLogger = false;

    private bool $isConfigured = false;

    /**
     * Logs grouped by InstrumentationScopes.
     *
     * @var array <string, Log[]>
     */
    protected array $logs           = [];

    /**
     * Span grouped by InstrumentationScopes.
     *
     * @var array <string, SpanInterface[]>
     */
    protected array $spans          = [];

    /**
     * InstrumentationScope.
     * @var array <string, InstrumentationScopeInterface>
     */
    protected array $instrumentationScopes = [];

    /**
     * Self instrumentation scope.
     */
    protected InstrumentationScopeInterface $selfInstrumentationScope;

    /**
     * Self trace.
     */
    protected Trace $selfTrace;

    public function __construct(
        protected ResourceInterface $systemResource,
        protected TelemetryContextResolverInterface $telemetryContextResolver,
        protected TelemetryFlushStrategyInterface|null $telemetryFlushStrategy = null,
        protected ExceptionFormatterInterface|null $exceptionFormatter = null,
        protected LoggerInterface|null $psrLogger = null
    ) {
        // Create self instrumentation scope
        $this->selfInstrumentationScope = new InstrumentationScope('tracer');
        $this->instrumentationScopes['i' . \spl_object_id($this->selfInstrumentationScope)] = $this->selfInstrumentationScope;
        $this->selfTrace            = new Trace($this->systemResource);

        if ($this->exceptionFormatter === null) {

            if (\interface_exists('IfCastle\Exceptions\BaseExceptionInterface')) {
                $this->exceptionFormatter = new BaseExceptionFormatter();
            } else {
                $this->exceptionFormatter = new ExceptionFormatter();
            }
        }
    }

    /**
     * Configure the Tracer before use.
     * This method can be called only once.
     */
    public function configure(
        ?bool $populateLogsAsSpanEvents = null,
        ?bool $populateExceptionsToLog = null,
        ?bool $populateExceptionsToSpan = null,
        ?bool $copyLogsToPsrLogger = null
    ): void {
        if ($this->isConfigured) {
            return;
        }

        $this->isConfigured                 = true;

        $this->populateLogsAsSpanEvents     = $populateLogsAsSpanEvents ?? $this->populateLogsAsSpanEvents;
        $this->populateExceptionsToLog      = $populateExceptionsToLog ?? $this->populateExceptionsToLog;
        $this->populateExceptionsToSpan     = $populateExceptionsToSpan ?? $this->populateExceptionsToSpan;
        $this->copyLogsToPsrLogger          = $copyLogsToPsrLogger ?? $this->copyLogsToPsrLogger;
    }

    /**
     * PSR-3 log adapter method.
     * Translates PSR-3 log messages into OpenTelemetry log.
     *
     * @param array<string,scalar|scalar[]> $context
     *
     */
    #[\Override]
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->registerLog($this->selfInstrumentationScope, $level, $message, $context);
    }

    #[\Override]
    public function addEvent(string $name, iterable $attributes = [], ?int $timestamp = null): void
    {
        $this->telemetryContextResolver
            ->resolveTelemetryContext()
            ->getCurrentTrace()
            ?->getCurrentSpan()
            ?->addEvent($name, $attributes, $timestamp);
    }

    #[\Override]
    public function getResource(): ResourceInterface
    {
        return $this->systemResource;
    }

    #[\Override]
    public function newTelemetryContext(): TelemetryContextInterface
    {
        return $this->telemetryContextResolver->newTelemetryContext();
    }

    #[\Override]
    public function createTrace(): TraceInterface
    {
        return new Trace($this->systemResource, null, $this->exceptionFormatter);
    }

    #[\Override]
    public function endTrace(TraceInterface $trace): void
    {
        $this->instrumentationScopes    = \array_merge($this->instrumentationScopes, $trace->getInstrumentationScopes());
        $this->spans                    = \array_merge_recursive($this->spans, $trace->getSpansByInstrumentationScope());
        $this->telemetryFlushStrategy?->flushTrace($trace);
    }

    #[\Override]
    public function registerLog(InstrumentationScopeInterface    $instrumentationScope,
        string                           $level,
        float|array|bool|int|string|null $body,
        iterable                         $attributes = []
    ): void {
        // ALGORITHM:
        // We place telemetry data into a preliminary container conforming to the OpenTelemetry standard
        // but do not serialize the data to save processor time.
        // The data will be serialized later, at the time of transmission,
        // in the background and will not impact the execution of the request.

        $telemetryContext           = $this->telemetryContextResolver->resolveTelemetryContext();

        if ($this->copyLogsToPsrLogger && $this->psrLogger !== null) {

            $context                = $attributes;

            if (\is_array($body)) {
                $context            = \array_merge($body, $context);

                if (\array_key_exists('exception', $context) && $context['exception'] instanceof \Throwable) {
                    $body           = $context['exception']->getMessage();
                } elseif (\array_key_exists('message', $context)) {
                    $body           = (string) $context['message'];
                } else {
                    $body           = 'no text record';
                }
            }

            $this->psrLogger->log($level, $body, $context);
        }

        if ($this->populateLogsAsSpanEvents) {
            $span                   = $telemetryContext->getCurrentTrace()?->getCurrentSpan();

            // If we have no current span, then we need to create a new span
            if ($span === null) {
                $span               = $this->createSpan('log', SpanKindEnum::INTERNAL, $instrumentationScope, $attributes);
            }

            $name                   = $level;
            $attributes['log.level'] = $level;

            if (!empty($attributes['log.subject'])) {
                $name               = $attributes['log.subject'];
                $attributes['log.report'] = $body;
            } elseif (!empty($attributes['exception.message'])) {
                $name               = $attributes['exception.message'];
                $attributes['log.report'] = $body;
            } elseif (\is_string($body)) {
                $name               = $body;
            }

            $span->addEvent($name, $attributes, SystemClock::now());
            return;
        }

        $logRecord                  = new Log(
            SystemClock::now(),
            $level,
            $body,
            $attributes,
            $telemetryContext->getTraceId(),
            $telemetryContext->getSpanId(),
            $telemetryContext->getTraceFlags()
        );

        // Collect logs in to structure:
        //
        // [*] ResourceLogs
        //     |- InstrumentationLogs
        //            |- LogRecords
        //

        $instrumentationScopeId     = 'i' . \spl_object_id($instrumentationScope);

        // Remember the InstrumentationScope for future use
        if (false === \array_key_exists($instrumentationScopeId, $this->instrumentationScopes)) {
            $this->instrumentationScopes[$instrumentationScopeId] = $instrumentationScope;
        }

        // So LogRecords are grouped by Resource (like /some/url/) and InstrumentationScope (like DataBase, HttpClient, Service).
        // And inherited from the current Span or Trace ResourceInfo.
        // So all logs for REST API request (or RPC, or WorkerRequest) will be grouped by ResourceInfo of the request.

        //
        // [*] TraceContext
        //     |- Trace
        //         |- ResourceInfo (like: /v2/endpoint/parameter/)
        //         |- SpanCollection (save to DB data, send email, etc.)
        //     |- Logs
        //         |- ResourceInfo (like: /v2/endpoint/parameter/)
        //              |- InstrumentationScope (like: database, email, etc.)
        //                  |- LogRecords.
        //
        // LogRecords can reference to TraceId and SpanId.
        //

        // All metrics will be grouped by ResourceInfo and InstrumentationScope.
        // But ResourceInfo is the same for all metrics

        if (false === \array_key_exists($instrumentationScopeId, $this->instrumentationScopes)) {
            $this->logs[$instrumentationScopeId] = [];
        }

        $this->logs[$instrumentationScopeId][] = $logRecord;
    }

    #[\Override]
    public function recordException(\Throwable $throwable, iterable $attributes = []): void
    {
        if ($this->populateExceptionsToLog) {
            $this->registerLog(
                $this->selfInstrumentationScope,
                $this->exceptionFormatter->getSeverityText($throwable),
                $this->exceptionFormatter->buildExceptionReport($throwable),
                $this->exceptionFormatter->buildExceptionAttributes($throwable, $attributes)
            );

            if ($this->populateLogsAsSpanEvents) {
                return;
            }
        }

        if (false === $this->populateExceptionsToSpan) {
            return;
        }

        $trace                      = $this->telemetryContextResolver->resolveTelemetryContext()->getCurrentTrace();

        if ($trace === null) {
            return;
        }

        $trace->getCurrentSpan()?->recordException($throwable, $attributes);
    }

    #[\Override]
    public function createSpan(
        string                        $spanName,
        SpanKindEnum                  $spanKind,
        ?InstrumentationScopeInterface $instrumentationScope = null,
        iterable                      $attributes = []
    ): SpanInterface {
        $trace                      = $this->telemetryContextResolver->resolveTelemetryContext()->getCurrentTrace() ?? $this->defineTrace();
        return $trace->createSpan($spanName, $spanKind, $instrumentationScope, $attributes);
    }

    #[\Override]
    public function endSpan(?SpanInterface $span = null): void
    {
        $this->telemetryContextResolver->resolveTelemetryContext()->getCurrentTrace()?->endSpan($span);
    }

    #[\Override]
    public function cleanTelemetry(): void
    {
        $this->logs                 = [];
        $this->spans                = [];
        $this->instrumentationScopes = [];
    }

    protected function defineTrace(): TraceInterface
    {
        return new Trace($this->systemResource);
    }
}

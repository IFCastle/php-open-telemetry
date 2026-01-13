<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry\Metrics;

use IfCastle\OpenTelemetry\InstrumentationScopeInterface;

interface MeterProviderInterface
{
    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     */
    public function registerCounter(
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $unit = null,
        ?string                       $description = null,
        iterable                      $attributes = [],
        bool                          $isReset = false
    ): MeterInterface;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     */
    public function registerUpDownCounter(
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $unit = null,
        ?string                       $description = null,
        iterable                      $attributes = [],
        bool                          $isReset = false
    ): MeterInterface;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     */
    public function registerGauge(
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $unit = null,
        ?string                       $description = null,
        iterable                      $attributes = [],
        bool                          $isReset = false
    ): MeterInterface;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     */
    public function registerHistogram(
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $unit = null,
        ?string                       $description = null,
        iterable                      $attributes = [],
        bool                          $isReset = false
    ): MeterInterface;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     */
    public function registerSummary(
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $unit = null,
        ?string                       $description = null,
        iterable                      $attributes = [],
        bool                          $isReset = false
    ): MeterInterface;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     */
    public function registerState(
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $description = null,
        iterable                      $attributes = [],
        bool                          $isReset = false
    ): StateInterface;
}

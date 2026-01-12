<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry\Metrics;

use IfCastle\OpenTelemetry\InstrumentationScopeInterface;

interface MeterProviderInterface
{
    /**
     * @param InstrumentationScopeInterface $instrumentationScope
     * @param string                        $name
     * @param string|null                   $unit
     * @param string|null                   $description
     * @param iterable<string, scalar|scalar[]> $attributes
     * @param bool                          $isReset
     *
     * @return MeterInterface
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
     * @param InstrumentationScopeInterface $instrumentationScope
     * @param string                        $name
     * @param string|null                   $unit
     * @param string|null                   $description
     * @param iterable<string, scalar|scalar[]> $attributes
     * @param bool                          $isReset
     *
     * @return MeterInterface
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
     * @param InstrumentationScopeInterface $instrumentationScope
     * @param string                        $name
     * @param string|null                   $unit
     * @param string|null                   $description
     * @param iterable<string, scalar|scalar[]> $attributes
     * @param bool                          $isReset
     *
     * @return MeterInterface
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
     * @param InstrumentationScopeInterface $instrumentationScope
     * @param string                        $name
     * @param string|null                   $unit
     * @param string|null                   $description
     * @param iterable<string, scalar|scalar[]> $attributes
     * @param bool                          $isReset
     *
     * @return MeterInterface
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
     * @param InstrumentationScopeInterface $instrumentationScope
     * @param string                        $name
     * @param string|null                   $unit
     * @param string|null                   $description
     * @param iterable<string, scalar|scalar[]> $attributes
     * @param bool                          $isReset
     *
     * @return MeterInterface
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
     * @param InstrumentationScopeInterface $instrumentationScope
     * @param string                        $name
     * @param string|null                   $description
     * @param iterable<string, scalar|scalar[]> $attributes
     * @param bool                          $isReset
     *
     * @return StateInterface
     */
    public function registerState(
        InstrumentationScopeInterface $instrumentationScope,
        string                        $name,
        ?string                       $description = null,
        iterable                      $attributes = [],
        bool                          $isReset = false
    ): StateInterface;
}

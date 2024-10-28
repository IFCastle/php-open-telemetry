<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

interface AttributesInterface
{
    public function setAttribute(string $key, string|bool|int|float|null $value): static;

    public function getAttribute(string $key): string|bool|int|float|null;

    /**
     * @return array<string, scalar|null>
     */
    public function getAttributes(): array;

    /**
     * @param iterable<string, scalar|null> $attributes
     *
     * @return $this
     */
    public function setAttributes(iterable $attributes): static;

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     * @return $this
     */
    public function addAttributes(iterable $attributes): static;

    public function hasAttribute(string $key): bool;

    /**
     *
     * @return array<string, scalar|null>
     */
    public function findByPrefix(string $prefix): array;

    public function findByPrefixFirst(string $prefix): ?string;
}

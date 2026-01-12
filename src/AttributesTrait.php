<?php

declare(strict_types=1);

namespace IfCastle\OpenTelemetry;

trait AttributesTrait
{
    /**
     * @var array<string, scalar|null>
     */
    protected array $attributes = [];

    public function setAttribute(string $key, string|bool|int|float|null $value): static
    {
        $this->attributes[$key]     = $value;

        return $this;
    }

    public function getAttribute(string $key): string|bool|int|float|null
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @return array<string, scalar|null>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param iterable<string, scalar|null> $attributes
     *
     * @return $this
     */
    public function setAttributes(iterable $attributes): static
    {
        if (!\is_array($attributes)) {
            $attributes             = \iterator_to_array($attributes);
        }

        $this->attributes           = $attributes;

        return $this;
    }

    /**
     * @param iterable<string, scalar|scalar[]> $attributes
     *
     * @return $this
     */
    public function addAttributes(iterable $attributes): static
    {
        if (!\is_array($attributes)) {
            $attributes             = \iterator_to_array($attributes);
        }

        $this->attributes           = \array_merge($this->attributes, $attributes);

        return $this;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     *
     * @return array<string, scalar|null>
     */
    public function findByPrefix(string $prefix): array
    {
        $prefix .= '.';

        $result                     = [];

        foreach ($this->attributes as $key => $value) {
            if (\str_starts_with($key, $prefix)) {
                $result[$key]       = $value;
            }
        }

        return $result;
    }

    public function findByPrefixFirst(string $prefix): ?string
    {
        $prefix .= '.';

        foreach ($this->attributes as $key => $value) {
            if (\str_starts_with($key, $prefix)) {
                return $value;
            }
        }

        return null;
    }
}

<?php

namespace TeamChallengeApps\Distance\Conversion;

use InvalidArgumentException;
use TeamChallengeApps\Distance\Unit;

class UnitPair {

    private Unit $source;
    private Unit $target;

    public function __construct(Unit|string $source, Unit|string $target)
    {
        $this->source = Unit::make($source);
        $this->target = Unit::make($target);
    }

    public static function make(string|self $units): static
    {
        if ( ! $units instanceof self ) {
            $units = static::createFromString($units);
        }

        return $units;
    }

    public static function createFromString(string $string): static
    {
        $parts = explode(':', $string);

        if ( count($parts) != 2 ) {
            throw new InvalidArgumentException('The unit pair must be in the format "source:target".');
        }

        return new static($parts[0], $parts[1]);
    }

    public function getSource(): Unit
    {
        return $this->source;
    }

    public function getTarget(): Unit
    {
        return $this->target;
    }

    public function flip(): static
    {
        return new static($this->target, $this->source);
    }

    public function areIdentical(): bool
    {
        return $this->source === $this->target;
    }

    public function toString(): string
    {
        return sprintf('%s:%s', $this->source->value, $this->target->value);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->toString();
    }

}

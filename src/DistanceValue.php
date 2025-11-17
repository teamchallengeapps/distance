<?php

namespace TeamChallengeApps\Distance;

use Livewire\Wireable;
use TeamChallengeApps\Distance\Concerns\Calculations;
use TeamChallengeApps\Distance\Concerns\Comparisons;
use TeamChallengeApps\Distance\Concerns\Conversions;
use ValueError;

class DistanceValue implements Wireable {

    use Calculations, Comparisons, Conversions;

    protected float|int $value;

    protected Unit $unit;

    protected array $equals = [];

    public function __construct(float|int $value, Unit|string $unit = null, array $equals = [])
    {
        $this->value = $value;
        $this->setUnit($unit ?? app(Config::class)->getBaseUnit());
        $this->setEquals($equals);
    }

    public function getValue(): float|int
    {
        return $this->value;
    }

    public function setValue(float|int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }

    public function setUnit(Unit|string $unit): self
    {
        $this->unit = Unit::make($unit);
        return $this;
    }

    public function getEquals(): array
    {
        return $this->equals ?? [];
    }

    public function setEquals(array $equals): self
    {
        foreach ( $equals as $unit => $value ) {
            $this->setEqualTo($unit, $value);
        }
        return $this;
    }

    public function hasEqualTo(string|Unit $unit): bool
    {
        return $this->getEqualTo($unit) !== null;
    }

    public function getEqualTo(string|Unit $unit): mixed
    {
        return $this->equals[Unit::make($unit)->value] ?? null;
    }

    public function setEqualTo(string|Unit $unit, float|int $value): self
    {
        $this->equals[Unit::make($unit)->value] = $value;
        return $this;
    }

    public function copy(): static
    {
        return clone $this;
    }

    /** @throws ValueError */
    protected static function expectUnit(Unit|string $unit): Unit
    {
        if ( $unit instanceof Unit ) {
            return $unit;
        }

        return Unit::from($unit);
    }

    public static function zero(Unit|string $unit = null): static
    {
        return new static(0, $unit);
    }

    public static function make(float|int $value, Unit|string $unit = null, array $equals = []): static
    {
        return new static($value, $unit, $equals);
    }

    public static function instance(self $distance): static
    {
        return clone $distance;
    }

    public function toData(): array
    {
        return [
            'value' => $this->value,
            'unit' => $this->unit->value,
            'equals' => $this->equals ?? [],
        ];
    }

    public static function fromData(array $data): static
    {
        return static::make($data['value'], $data['unit'], $data['equals'] ?? []);
    }

    public function toLivewire()
    {
        return $this->toData();
    }

    public static function fromLivewire($value)
    {
        return static::fromData($value);
    }

    public function format(bool $convert = true, array $options = []): string
    {
        return app(DistanceFormatter::class)->format($this, $convert, $options);
    }

    public function formatDecimal(bool $convert = true, array $options = []): int|float
    {
        return $this->formatUsing('decimal', $convert, $options);
    }

    public function formatUsing(string $formatter, bool $convert = true, array $options = []): int|float|string
    {
        return app(DistanceFormatter::class)->formatUsing($formatter, $this, $convert, $options);
    }

    public function toString(): string
    {
        return (string) $this->format(convert: false);
    }

    public function toDecimal(int $precision = null): int|float
    {
        return $this->formatDecimal(convert: false, options: $precision ? ['precision' => $precision] : []);
    }

    public function getHash(): string
    {
        return sha1(serialize($this->toData()));
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}

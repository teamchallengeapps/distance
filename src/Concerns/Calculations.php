<?php

namespace TeamChallengeApps\Distance\Concerns;

use InvalidArgumentException;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Exceptions\CalculationException;

trait Calculations {

    public function add(self $distance, bool $calculateEquals = true): static
    {
        /** @var DistanceValue $this */
        if ( ! $this->isUnit($distance->getUnit()) ) {
            throw CalculationException::differentUnits();
        }

        if ( $distance->isZero() ) {
            return $this->copy();
        }

        $equals = [];

        if ( $calculateEquals ) {
            $equals = collect($this->getEquals())->filter(function($value, $unit) use($distance) {
                return $distance->hasEqualTo($unit);
            })->map(function($value, $unit) use($distance) {
                return $value + $distance->getEqualTo($unit);
            })->toArray();
        }

        return new static(
            value: $this->getValue() + $distance->getValue(),
            unit: $this->getUnit(),
            equals: $equals
        );
    }

    public function subtract(self $distance, bool $calculateEquals = true): static
    {
        /** @var DistanceValue $this */
        if ( ! $this->isUnit($distance->getUnit()) ) {
            throw CalculationException::differentUnits();
        }

        if ( $distance->isZero() ) {
            return $this->copy();
        }

        $equals = [];

        if ( $calculateEquals ) {
            $equals = collect($this->getEquals())->filter(function($value, $unit) use($distance) {
                return $distance->hasEqualTo($unit);
            })->map(function($value, $unit) use($distance) {
                return $value - $distance->getEqualTo($unit);
            })->toArray();
        }

        return new static(
            value: $this->getValue() - $distance->getValue(),
            unit: $this->getUnit(),
            equals: $equals
        );
    }

    public function multiply(int|float $multiplier, bool $calculateEquals = true): static
    {
        if (!is_numeric($multiplier)) {
            throw new InvalidArgumentException(sprintf('Multiplier should be a numeric value, "%s" given.', gettype($multiplier)));
        }

        $equals = [];

        if ( $calculateEquals ) {
            $equals = collect($this->getEquals())->map(function($value) use($multiplier) {
                return $value * $multiplier;
            })->toArray();
        }

        return new static(
            value: $this->getValue() * $multiplier,
            unit: $this->getUnit(),
            equals: $equals
        );
    }

    public function divide(int|float $divisor, bool $calculateEquals = true): static
    {
        if (!is_numeric($divisor)) {
            throw new InvalidArgumentException(sprintf('Divisor should be a numeric value, "%s" given.', gettype($divisor)));
        }

        $equals = [];

        if ( $calculateEquals ) {
            $equals = collect($this->getEquals())->map(function($value) use($divisor) {
                return $value / $divisor;
            })->toArray();
        }

        return new static(
            value: $this->getValue() / $divisor,
            unit: $this->getUnit(),
            equals: $equals
        );
    }

    public function percentageOf(self $distance, $overflow = true): float
    {
        if ($distance->isZero()) {
            return 0;
        }

        if ( ! $this->isUnit($distance->getUnit()) ) {
            throw CalculationException::differentUnits();
        }

        $percentage = ($this->getValue() / $distance->getValue());

        if ($overflow) {
            return floor($percentage * 100);
        }

        if ($percentage >= 1) {
            return 100;
        }

        if ($percentage >= 0.99) {
            return 99;
        }

        return floor($percentage * 100);
    }

}

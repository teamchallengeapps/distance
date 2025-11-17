<?php

namespace TeamChallengeApps\Distance\Concerns;

use Illuminate\Support\Arr;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Exceptions\ComparisonException;
use TeamChallengeApps\Distance\Unit;

trait Comparisons {

    public function equals(self $distance, bool $checkEquals = true): bool
    {
        /** @var DistanceValue $this */
        if ( $this->isUnit($distance->getUnit()) && ( $this->getValue() == $distance->getValue() ) ) {
            return true;
        }

        if ( ! $checkEquals ) {
            return false;
        }

        if ( $this->hasEqualTo($distance->getUnit()) && $this->getEqualTo($distance->getUnit()) == $distance->getValue() ) {
            return true;
        }

        if ( $distance->hasEqualTo($this->getUnit()) && $distance->getEqualTo($this->getUnit()) == $this->getValue() ) {
            return true;
        }

        return false;
    }

    public function greaterThan(self $distance)
    {
        /** @var DistanceValue $this */
        if ( ! $this->isUnit($distance->getUnit()) ) {
            throw ComparisonException::differentUnits();
        }

        return $this->getValue() > $distance->getValue();
    }

    public function gt(self $distance)
    {
        return $this->greaterThan($distance);
    }

    public function greaterThanOrEqual(self $distance)
    {
        /** @var DistanceValue $this */
        if ( ! $this->isUnit($distance->getUnit()) ) {
            throw ComparisonException::differentUnits();
        }

        return $this->getValue() >= $distance->getValue();
    }

    public function gte(self $distance)
    {
        return $this->greaterThanOrEqual($distance);
    }

    public function lessThan(self $distance)
    {
        /** @var DistanceValue $this */
        if ( ! $this->isUnit($distance->getUnit()) ) {
            throw ComparisonException::differentUnits();
        }

        return $this->getValue() < $distance->getValue();
    }

    public function lt(self $distance)
    {
        return $this->lessThan($distance);
    }

    public function lessThanOrEqual(self $distance)
    {
        /** @var DistanceValue $this */
        if ( ! $this->isUnit($distance->getUnit()) ) {
            throw ComparisonException::differentUnits();
        }

        return $this->getValue() <= $distance->getValue();
    }

    public function lte(self $distance)
    {
        return $this->lessThanOrEqual($distance);
    }

    public function isZero(): bool
    {
        /** @var DistanceValue $this */
        return $this->value === 0;
    }

    public function isPositive()
    {
        /** @var DistanceValue $this */
        return $this->value > 0;
    }

    public function isNegative()
    {
        /** @var DistanceValue $this */
        return $this->value < 0;
    }

    public function isUnit(string|Unit $unit): bool
    {
        /** @var DistanceValue $this */
        return $this->unit == Unit::make($unit);
    }

}

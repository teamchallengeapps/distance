<?php

namespace TeamChallengeApps\Distance;

use TeamChallengeApps\Distance\Conversion\Conversion;
use TeamChallengeApps\Distance\Conversion\UnitPair;

class Config {

    protected Unit $baseUnit;
    protected Unit $displayUnit;
    protected array $conversions;

    public function __construct(
        Unit|string $baseUnit = null,
        Unit|string $displayUnit = null,
        array $conversions = [],
    )
    {
        $this->baseUnit = $baseUnit ? Unit::make($baseUnit) : Unit::Centimeters;
        $this->displayUnit = $displayUnit ? Unit::make($baseUnit) : $this->baseUnit;
        foreach ( $conversions as $units => $ratio ) {
            $this->setConversion($units, $ratio);
        }
    }

    public function getBaseUnit(): Unit
    {
        return $this->baseUnit;
    }

    public function setBaseUnit(Unit $baseUnit): void
    {
        $this->baseUnit = $baseUnit;
    }

    public function getDisplayUnit(): Unit
    {
        return $this->displayUnit;
    }

    public function setDisplayUnit(Unit $displayUnit): void
    {
        $this->displayUnit = $displayUnit;
    }

    public function setConversion(string|UnitPair $units, float $ratio, $setReverse = true): void
    {
        $units = UnitPair::make($units);
        $this->conversions[$units->toString()] = new Conversion($units, $ratio);
        if ( $setReverse ) {
            $reverse = $units->flip();
            $this->conversions[$reverse->toString()] = new Conversion($reverse, 1 / $ratio);
        }
    }

    public function getConversion(string|UnitPair $units): ?Conversion
    {
        $units = UnitPair::make($units);
        return $this->conversions[$units->toString()] ?? null;
    }

}

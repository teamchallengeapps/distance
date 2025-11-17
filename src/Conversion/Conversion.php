<?php

namespace TeamChallengeApps\Distance\Conversion;

use InvalidArgumentException;

class Conversion {

    protected UnitPair $units;
    protected float $ratio;

    public function __construct(UnitPair $units, float $ratio)
    {
        if ( $units->areIdentical() ) {
            throw new InvalidArgumentException('The units cannot be identical');
        }
        $this->units = $units;
        $this->ratio = $ratio;
    }

    public function getUnits(): UnitPair
    {
        return $this->units;
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }

}

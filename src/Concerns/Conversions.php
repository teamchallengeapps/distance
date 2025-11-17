<?php

namespace TeamChallengeApps\Distance\Concerns;

use TeamChallengeApps\Distance\Converter;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Unit;

trait Conversions {

    public function convertTo(string|Unit $unit, bool $checkEquals = true): DistanceValue
    {
        /** @var DistanceValue $this */
        return app(Converter::class)->convertTo(
            distance: $this,
            unit: $unit,
            checkEquals: $checkEquals
        );
    }

    public function convertToBase(): DistanceValue
    {
        /** @var DistanceValue $this */
        return app(Converter::class)->convertToBase(
            distance: $this
        );
    }

}

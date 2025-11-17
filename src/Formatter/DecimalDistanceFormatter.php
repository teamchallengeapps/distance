<?php

namespace TeamChallengeApps\Distance\Formatter;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\RoundingMode;

class DecimalDistanceFormatter implements DistanceFormatter {

    public function format(DistanceValue $distance, array $options = []): int|float
    {
        $precision = Arr::get($options, 'precision') ?: config('distance.formatting.precision.'.$distance->getUnit()->value, 2);
        $round = Arr::get($options, 'round') ?: config('distance.formatting.round.'.$distance->getUnit()->value);

        if ( ! is_int($precision) ) {
            throw new InvalidArgumentException('Invalid argument - precision should be int');
        }

        if ( ! is_null($round) && ! $round instanceof RoundingMode ) {
            throw new InvalidArgumentException('Invalid argument - round should be instance of RoundingMode');
        }

        if ( $precision === 0 ) {
            return (int) match ( $round ) {
                RoundingMode::UP => round($distance->getValue(), $precision, PHP_ROUND_HALF_UP),
                RoundingMode::DOWN => round($distance->getValue(), $precision, PHP_ROUND_HALF_DOWN),
                RoundingMode::CEILING, null => ceil($distance->getValue()),
                RoundingMode::FLOOR => floor($distance->getValue()),
            };
        }

        return match ( $round ) {
            RoundingMode::UP, null => round($distance->getValue(), $precision, PHP_ROUND_HALF_UP),
            RoundingMode::DOWN => round($distance->getValue(), $precision, PHP_ROUND_HALF_DOWN),
        };
    }

}

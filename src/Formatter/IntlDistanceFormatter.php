<?php

namespace TeamChallengeApps\Distance\Formatter;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use NumberFormatter;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\RoundingMode;

class IntlDistanceFormatter implements DistanceFormatter {

    protected NumberFormatter $formatter;

    public function __construct(NumberFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function format(DistanceValue $distance, array $options = []): string
    {
        $precision = Arr::get($options, 'precision') ?: config('distance.formatting.precision.'.$distance->getUnit()->value, 2);
        $round = Arr::get($options, 'round') ?: config('distance.formatting.round.'.$distance->getUnit()->value);

        if ( ! is_int($precision) ) {
            throw new InvalidArgumentException('Invalid argument - precision should be int');
        }

        if ( ! is_null($round) && ! $round instanceof RoundingMode ) {
            throw new InvalidArgumentException('Invalid argument - round should be instance of RoundingMode');
        }

        $value = app(\TeamChallengeApps\Distance\DistanceFormatter::class)->driver('decimal')->format($distance, $options);

        $this->formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $precision);
        $this->formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);

        $string = $this->formatter->format($value);

        if ( Arr::get($options, 'unit', true) && config('distance.formatting.translation.choice') ) {
            $string .= ' '. ( Arr::get($options, 'short', false) ? $distance->getUnit()->transShortChoice($value) : $distance->getUnit()->transChoice($value) );
        } elseif ( Arr::get($options, 'unit', true) ) {
            $string .= ' '. ( Arr::get($options, 'short', false) ? $distance->getUnit()->transShort() : $distance->getUnit()->trans() );
        }

        return $string;
    }

}

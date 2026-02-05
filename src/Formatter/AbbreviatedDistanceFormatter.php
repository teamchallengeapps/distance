<?php

namespace TeamChallengeApps\Distance\Formatter;

use Illuminate\Support\Arr;
use NumberFormatter;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\RoundingMode;
use TeamChallengeApps\Distance\Unit;

class AbbreviatedDistanceFormatter implements DistanceFormatter {

    const SHORT = 'short';
    const TINY = 'tiny';

    protected NumberFormatter $formatter;

    public function __construct(NumberFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function format(DistanceValue $distance, array $options = []): string
    {
        $suffixType = Arr::get($options, 'suffix', self::TINY);
        $originalValue = $value = $distance->getValue();

        $precision = Arr::get($options, 'precision', 0);
        $round = Arr::get($options, 'round', RoundingMode::UP);

        if ( ! is_int($precision) ) {
            throw new InvalidArgumentException('Invalid argument - precision should be int');
        }

        if ( ! $round instanceof RoundingMode ) {
            throw new InvalidArgumentException('Invalid argument - round should be instance of RoundingMode');
        }

        $suffix = '';

        if ( $distance->getUnit() == Unit::Footsteps ) {
            $groups = ['', 'thousand', 'million', 'billion', 'trillion'];
            for ($i = 0; $value >= 1000; $i++) {
                $value /= 1000;
            }
            $suffix = !empty($groups[$i]) ? __('distance::number.'.$groups[$i].'_'.match($suffixType) {
                    self::SHORT => self::SHORT,
                    self::TINY => self::TINY,
                    default => self::TINY,
                }) : '';
        }

        if ( $precision === 0 ) {
            $value = match ( $round ) {
                RoundingMode::UP => round($value, $precision, PHP_ROUND_HALF_UP),
                RoundingMode::DOWN => round($value, $precision, PHP_ROUND_HALF_DOWN),
                RoundingMode::CEILING, null => ceil($value),
                RoundingMode::FLOOR => floor($value),
                default => ceil($value),
            };
        } else {
            $value = match ( $round ) {
                RoundingMode::UP, null => round($value, $precision, PHP_ROUND_HALF_UP),
                RoundingMode::DOWN => round($value, $precision, PHP_ROUND_HALF_DOWN),
                default => round($value, $precision, PHP_ROUND_HALF_UP),
            };
        }

        $this->formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $precision);
        $this->formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);

        $string = $this->formatter->format($value).$suffix;

        if ( Arr::get($options, 'unit', true) && config('distance.formatting.translation.choice') ) {
            $string .= ' '.$distance->getUnit()->transShortChoice($originalValue);
        }  else if ( Arr::get($options, 'unit', true) ) {
            $string .= ' '.$distance->getUnit()->transShort();
        }

        return $string;
    }

}

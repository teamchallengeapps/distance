<?php

namespace TeamChallengeApps\Distance\Formatter;

use Illuminate\Support\Arr;
use NumberFormatter;
use TeamChallengeApps\Distance\DistanceValue;
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

        $this->formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
        $this->formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);

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

        $string = $this->formatter->format($value).$suffix;

        if ( Arr::get($options, 'unit', true) && config('distance.formatting.translation.choice') ) {
            $string .= ' '.$distance->getUnit()->transShortChoice($originalValue);
        }  else if ( Arr::get($options, 'unit', true) ) {
            $string .= ' '.$distance->getUnit()->transShort();
        }

        return $string;
    }

}

<?php

namespace TeamChallengeApps\Distance;

use Countable;

enum Unit: string {

    case Centimeters = 'centimeters';
    case Meters = 'meters';
    case Kilometers = 'kilometers';
    case Miles = 'miles';
    case Footsteps = 'footsteps';
    case Inches = 'inches';

    public static function toName(self|string $unit): string
    {
        return $unit instanceof self ? $unit->value : $unit;
    }

    public static function make(self|string $unit): static
    {
        if ( $unit instanceof self ) {
            return $unit;
        }

        return static::from($unit);
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function trans(): string
    {
        return __('distance::units.'.$this->value);
    }

    public function transChoice(Countable|int|float $number): string
    {
        return trans_choice('distance::units.'.$this->value, $number);
    }

    public function transShort(): string
    {
        return __('distance::units.'.$this->value.'_short');
    }

    public function transShortChoice(Countable|int|float $number): string
    {
        return trans_choice('distance::units.'.$this->value.'_short', $number);
    }

}

<?php

namespace TeamChallengeApps\Distance\Exceptions;

class CalculationException extends \Exception {

    public static function differentUnits()
    {
        return new static('The calculation units are different');
    }

}

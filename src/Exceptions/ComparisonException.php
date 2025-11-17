<?php

namespace TeamChallengeApps\Distance\Exceptions;

class ComparisonException extends \Exception {

    public static function differentUnits()
    {
        return new static('The comparison units are different');
    }

}

<?php

use TeamChallengeApps\Distance\Distance;

if (!function_exists('distance_value')) {
    function distance_value($value, $unit = 'meters')
    {
        if (!$value instanceof Distance) {
            $value = new Distance($value, $unit);
        }

        return $value;
    }
}

if (!function_exists('distance_get')) {
    function distance_get($value, $unit = 'meters', $from = 'meters')
    {
        if (!$value instanceof Distance) {
            $value = new Distance($value, $from);
        }

        return $value->asUnit($unit);
    }
}

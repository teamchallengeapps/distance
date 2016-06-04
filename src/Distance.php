<?php

namespace TeamChallengeApps\Distance;

use Illuminate\Support\Arr;
use App\Exceptions\DistanceException;
use Exception;

class Distance
{
    public $value;
    public $unit;

    private $config = null;

    /* Constructors */

    public function __construct($value, $unit = 'meters', $config = null)
    {
        $this->setDistance($value, $unit);

        if ( ! is_null($config) ) {
            $this->config = $config;
        }
    }

    public static function make($distance, $unit = 'meters')
    {
        return new static($distance, $unit, $this->config);
    }

    public static function fromMeters($distance)
    {
        return new static($distance, 'meters', $this->config);
    }

    public static function fromKilometers($distance)
    {
        return new static($distance, 'kilometers', $this->config);
    }

    public static function fromMiles($distance)
    {
        return new static($distance, 'miles', $this->config);
    }

    public static function fromFootsteps($distance)
    {
        return new static($distance, 'footsteps', $this->config);
    }

    public static function fromSteps($distance)
    {
        return static::fromFootsteps($distance);
    }

    public function copy()
    {
        return new static($this->value, $this->unit, $this->config);
    }

    /* Getters and Setters */

    public function setValue($value, $unit)
    {
        $this->value = $value;
        $this->unit = $unit;

        return $this;
    }

    public function setDistance($distance, $unit)
    {
        return $this->setValue($distance, $unit);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getDistance()
    {
        return $this->getValue();
    }

    public function getUnit()
    {
        return $this->unit;
    }

    /* Helpers */

    public function isZero()
    {
        return $this->value == 0;
    }

    public function isEmpty()
    {
        return $this->isZero();
    }

    public function percentageOf(Distance $distance, $overflow = true)
    {
        $percentage = ( $this->asBase() / $distance->asBase() );

        if ( $overflow ) {
            return round($percentage * 100);
        }

        if ($percentage >= 1) {
            return '100';
        }

        if ($percentage >= 0.99) {
            return '99';
        }

        return round($percentage * 100);
    }

    public function lt(Distance $distance)
    {
        return ( $this->asBase() < $distance->asBase() );
    }

    public function lte(Distance $distance)
    {
        return ( $this->asBase() <= $distance->asBase() );
    }

    public function gt(Distance $distance)
    {
        return ( $this->asBase() > $distance->asBase() );
    }

    public function gte(Distance $distance)
    {
        return ( $this->asBase() >= $distance->asBase() );
    }

    public function isUnit($unit)
    {
        return ( $this->unit == $unit );
    }

    public function isMeters()
    {
        return $this->isUnit('meters');
    }

    public function isKilometers()
    {
        return $this->isUnit('kilometers');
    }

    public function isFootsteps()
    {
        return $this->isUnit('footsteps');
    }

    public function isMiles()
    {
        return $this->isUnit('miles');
    }

    public function isSteps()
    {
        return $this->isFootsteps();
    }

    public function isBaseUnit()
    {
        return $this->isMeters();
    }

    public function units()
    {
        return array_keys($this->config('units'));
    }

    public function getDecimals()
    {
        $key = 'units.' . $this->unit . '.decimals';

        return $this->config($key, 2);
    }

    public function getMeasurement($unit = null)
    {
        $unit = $unit ? $unit : $this->unit;
        $key = 'units.' . $unit . '.unit';

        $measurement = $this->config($key);

        if ( ! $measurement ) {
            throw new Exception('Measurement ' . $unit . ' not found');
        }

        return $measurement;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    public function config($key = null, $fallback = null)
    {
        if (is_null($this->config)) {
            $this->loadConfig();
        }

        if (is_null($key)) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $fallback);
    }

    protected function loadConfig()
    {
        if ( ! function_exists('config') ) {
            throw new Exception('Unable to auto load config');
        }

        $this->config = config('distance');
    }

    /* Modifiers */

    public function decrement(Distance $distance)
    {
        $value = new static($this->asBase() - $distance->asBase(), 'meters', $this->config);

        $this->value = $value->asUnit($this->unit);

        return $this;
    }

    public function increment(Distance $distance)
    {
        $value = new static($this->asBase() + $distance->asBase(), 'meters', $this->config);

        $this->value = $value->asUnit($this->unit);

        return $this;
    }

    /* Conversions */

    public function toBase()
    {
        if ( $this->isMeters() ) {
            return $this;
        }

        return $this->convertTo($this->baseUnit);
    }

    public function base()
    {
        return $this->toBase();
    }

    public function asBase()
    {
        if ( $this->isMeters() ) {
            return $this->value;
        }

        return $this->asUnit('meters');
    }

    public function asUnit($unit)
    {
        $from = $this->getMeasurement($this->unit);
        $to = $this->getMeasurement($unit);

        return ( $this->value * $to * (1 / $from) );
    }

    public function convertTo($unit)
    {
        $distance = $this->asUnit($unit);

        return new static($distance, $unit, $this->config);
    }

    public function toMeters()
    {
        return $this->convertTo('meters');
    }

    public function toKilometers()
    {
        return $this->convertTo('kilometers');
    }

    public function toMiles()
    {
        return $this->convertTo('miles');
    }

    public function toFootsteps()
    {
        return $this->convertTo('footsteps');
    }

    public function toSteps()
    {
        return $this->toFootsteps();
    }

    /* Formatting */

    public function all()
    {
        $units = new DistanceCollection();

        foreach ( $this->units() as $unit ) {
            $units->put($unit, $this->convertTo($unit));
        }

        return $units;
    }

    public function toArray()
    {
        return $this->all()->toArray();
    }

    public function toRoundedArray()
    {
        return $this->all()->map(function($unit){
            return $unit->round();
        });
    }

    public function round()
    {
        return round($this->value, $this->getDecimals());
    }

    public function toString()
    {
        return (string) number_format($this->value, $this->getDecimals(), '.', ',');
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function __get($property)
    {
        if (in_array($property, $this->units())) {
            $unit = $property;
            return $this->convertTo($unit)->distance;
        }
        
        return null;
    }

}

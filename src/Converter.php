<?php

namespace TeamChallengeApps\Distance;

use Illuminate\Support\Arr;
use TeamChallengeApps\Distance\Conversion\UnitPair;
use TeamChallengeApps\Distance\Exceptions\ConversionException;

class Converter {

    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function convertTo(DistanceValue $distance, string|Unit $unit, bool $checkEquals = true): DistanceValue
    {
        $unit = Unit::make($unit);

        if ( $distance->getUnit() == $unit ) {
            return $distance->copy();
        }

        if ( $checkEquals && Arr::has($distance->getEquals(), $unit->value) ) {
            return new DistanceValue(
                value: Arr::get($distance->getEquals(), $unit->value),
                unit: $unit,
                equals: array_merge(
                    Arr::except($distance->getEquals(), $unit->value),
                    [ $distance->getUnit()->value => $distance->getValue()]
                ),
            );
        }

        $units = new UnitPair($distance->getUnit(), $unit);

        $conversion = $this->config->getConversion($units);

        if ( ! $conversion && $this->canConvertToBase($distance) ) {
            return $this->convertThroughBase($distance, $unit, $checkEquals);
        }

        if ( ! $conversion ) {
            throw new ConversionException('Unable to convert from '.$units->getSource()->value.' to '.$units->getTarget()->value);
        }

        // TODO
        //  bool $convertEquals = true

        return new DistanceValue(
            value: $distance->getValue() * $conversion->getRatio(),
            unit: $unit,
            equals: Arr::except($distance->getEquals(), $unit->value),
        );
    }

    public function convertToBase(DistanceValue $distance, bool $checkEquals = true)
    {
        return $this->convertTo($distance, $this->config->getBaseUnit(), $checkEquals);
    }

    protected function convertThroughBase(DistanceValue $distance, Unit $unit, bool $checkEquals = true): DistanceValue
    {
        $unit = Unit::make($unit);
        $baseUnit = $this->config->getBaseUnit();

        if ( $distance->isUnit($baseUnit) ) {
            throw new ConversionException('Unable to convert - distance is already '.$unit->value);
        }

        $units = new UnitPair($baseUnit, $unit);
        $conversion = $this->config->getConversion($units);

        if ( ! $conversion ) {
            throw new ConversionException('Unable to convert from '.$distance->getUnit()->value.' to '.$unit->value.' through '.$baseUnit->value);
        }

        return $this->convertTo(
            distance: $this->convertToBase($distance),
            unit: $unit,
            checkEquals: $checkEquals
        );
    }

    protected function canConvertToBase(DistanceValue $distance)
    {
        if ( $distance->getUnit() == $this->config->getBaseUnit()  ) {
            return false;
        }

        $units = new UnitPair($distance->getUnit(), $this->config->getBaseUnit());
        return ! is_null($this->config->getConversion($units));
    }

}

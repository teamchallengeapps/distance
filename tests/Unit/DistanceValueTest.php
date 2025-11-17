<?php

namespace TeamChallengeApps\Distance\Tests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Exceptions\CalculationException;
use TeamChallengeApps\Distance\Unit;

class DistanceValueTest extends TestCase
{
    /** @test */
    public function it_creates_distance()
    {
        $distance = new DistanceValue(50, Unit::Meters);

        $this->assertEquals($distance->getValue(), 50);
        $this->assertEquals($distance->getUnit(), Unit::Meters);

        $distance = DistanceValue::make(100, 'meters');

        $this->assertEquals($distance->getValue(), 100);
        $this->assertEquals($distance->getUnit(), Unit::Meters);
    }

    /** @test */
    public function it_creates_and_checks_zero()
    {
        $zero = DistanceValue::zero();
        $this->assertEquals(0, $zero->getValue());
        $this->assertTrue($zero->isZero());

        $zero = new DistanceValue(0, Unit::Meters);
        $this->assertEquals(0, $zero->getValue());
        $this->assertTrue($zero->isZero());
    }

    /** @test */
    public function it_creates_with_units()
    {
        $enums = [
            Unit::Centimeters,
            Unit::Meters,
            Unit::Kilometers,
            Unit::Miles,
            Unit::Footsteps,
        ];

        foreach ( $enums as $unit ) {
            $distance = new DistanceValue(100, $unit);
            $this->assertEquals($unit, $distance->getUnit());
        }

        $strings = [
            'centimeters',
            'meters',
            'kilometers',
            'miles',
            'footsteps',
        ];

        foreach ( $strings as $unit ) {
            $distance = new DistanceValue(100, $unit);
            $this->assertEquals(Unit::from($unit), $distance->getUnit());
        }
    }

    /** @test */
    public function it_throws_exception_with_unknown_unit()
    {
        $this->expectException(\ValueError::class);
        $distance = new DistanceValue(100, 'thingmibobs');
    }

    /** @test */
    public function it_can_get_unit_and_value()
    {
        $distance = new DistanceValue($value = 500, $unit = Unit::Meters);
        $this->assertEquals($unit, $distance->getUnit());
        $this->assertEquals($value, $distance->getValue());

        $distance = new DistanceValue($value = 0.5, $unit = Unit::Kilometers);
        $this->assertEquals($unit, $distance->getUnit());
        $this->assertEquals($value, $distance->getValue());
    }

    /** @test */
    public function it_can_copy()
    {
        $distanceOne = new DistanceValue($value = 500, $unit = Unit::Meters);
        $distanceTwo = $distanceOne->copy();

        $this->assertEquals($distanceOne, $distanceTwo);

        $distanceTwo->setValue(100);
        $this->assertNotEquals($distanceOne, $distanceTwo);
    }

    /** @test */
    public function it_compares_equal()
    {
        $this->assertTrue((new DistanceValue(20, 'meters'))->equals(new DistanceValue(20, 'meters')));

        $this->assertFalse((new DistanceValue(20, 'meters'))->equals(new DistanceValue(10, 'meters')));
        $this->assertFalse((new DistanceValue(10, 'meters'))->equals(new DistanceValue(20, 'meters')));

        $this->assertFalse((new DistanceValue(20, 'meters'))->equals(new DistanceValue(20, 'kilometers')));
        $this->assertFalse((new DistanceValue(20, 'kilometers'))->equals(new DistanceValue(20, 'meters')));

        $this->assertTrue((new DistanceValue(20, 'meters', equals: ['footsteps' => 25]))->equals(new DistanceValue(25, 'footsteps'), checkEquals: true));
        $this->assertFalse((new DistanceValue(20, 'meters', equals: ['footsteps' => 20]))->equals(new DistanceValue(25, 'footsteps'), checkEquals: true));
        $this->assertFalse((new DistanceValue(20, 'meters'))->equals(new DistanceValue(25, 'footsteps'), checkEquals: false));

        $this->assertTrue((new DistanceValue(25, 'footsteps'))->equals((new DistanceValue(20, 'meters', equals: ['footsteps' => 25])), checkEquals: true));
        $this->assertFalse((new DistanceValue(25, 'footsteps'))->equals((new DistanceValue(20, 'meters', equals: ['footsteps' => 20])), checkEquals: true));
        $this->assertFalse((new DistanceValue(25, 'footsteps'))->equals((new DistanceValue(20, 'meters')), checkEquals: false));
    }

    /** @test */
    public function it_throws_exception_when_subtracting_different_units()
    {
        $this->expectException(CalculationException::class);
        $result = (new DistanceValue(15, 'meters'))->subtract(new DistanceValue(3, 'miles'));
    }

    /** @test */
    public function it_can_do_subtraction_with_same_units()
    {
        $this->assertEquals(
            new DistanceValue(12, 'meters'),
            (new DistanceValue(15, 'meters'))->subtract(new DistanceValue(3, 'meters')),
        );

        $this->assertEquals(
            new DistanceValue(5, 'meters', ['footsteps' => 6]),
            (new DistanceValue(10, 'meters', ['footsteps' => 12]))->subtract(new DistanceValue(5, 'meters', ['footsteps' => 6])),
        );

        $this->assertEquals(
            new DistanceValue(5, 'meters'),
            (new DistanceValue(10, 'meters', ['footsteps' => 12]))->subtract(
                distance: new DistanceValue(5, 'meters', ['footsteps' => 6]),
                calculateEquals: false
            ),
        );
    }

    /** @test */
    public function it_throws_exception_when_adding_different_units()
    {
        $this->expectException(CalculationException::class);
        $result = (new DistanceValue(15, 'meters'))->add(new DistanceValue(3, 'miles'));
    }

    /** @test */
    public function it_can_do_addition_with_same_units()
    {
        $this->assertEquals(
            new DistanceValue(18, 'meters'),
            (new DistanceValue(15, 'meters'))->add(new DistanceValue(3, 'meters')),
        );

        $this->assertEquals(
            new DistanceValue(15, 'meters', ['footsteps' => 18]),
            (new DistanceValue(10, 'meters', ['footsteps' => 12]))->add(new DistanceValue(5, 'meters', ['footsteps' => 6])),
        );

        $this->assertEquals(
            new DistanceValue(15, 'meters'),
            (new DistanceValue(10, 'meters', ['footsteps' => 12]))->add(
                distance: new DistanceValue(5, 'meters', ['footsteps' => 6]),
                calculateEquals: false
            ),
        );
    }

    /** @test */
    public function it_can_do_multiplication()
    {
        $this->assertEquals(
            new DistanceValue(15, 'meters'),
            (new DistanceValue(5, 'meters'))->multiply(3),
        );

        $this->assertEquals(
            new DistanceValue(12.5, 'meters'),
            (new DistanceValue(5, 'meters'))->multiply(2.5),
        );

        $this->assertEquals(
            new DistanceValue(15, 'meters', ['footsteps' => 18]),
            (new DistanceValue(5, 'meters', ['footsteps' => 6]))->multiply(3),
        );

        $this->assertEquals(
            new DistanceValue(30, 'meters'),
            (new DistanceValue(10, 'meters', ['footsteps' => 12]))->multiply(
                multiplier: 3,
                calculateEquals: false
            )
        );
    }

    /** @test */
    public function it_can_do_division()
    {
        $this->assertEquals(
            new DistanceValue(5, 'meters'),
            (new DistanceValue(15, 'meters'))->divide(3),
        );

        $this->assertEquals(
            new DistanceValue(5, 'meters'),
            (new DistanceValue(12.5, 'meters'))->divide(2.5),
        );

        $this->assertEquals(
            new DistanceValue(5, 'meters', ['footsteps' => 6]),
            (new DistanceValue(15, 'meters', ['footsteps' => 18]))->divide(3),
        );

        $this->assertEquals(
            new DistanceValue(10, 'meters'),
            (new DistanceValue(30, 'meters', ['footsteps' => 36]))->divide(
                divisor: 3,
                calculateEquals: false
            )
        );
    }

    /** @test */
    public function it_can_represent_as_data_array()
    {
        $distance = new DistanceValue($value = 500, $unit = Unit::Meters);
        $data = $distance->toData();

        $this->assertEquals($data['value'], $value);
        $this->assertEquals($data['unit'], $unit->value);

        $this->assertEquals($distance, DistanceValue::fromData($data));
    }
}

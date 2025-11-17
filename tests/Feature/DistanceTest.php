<?php

namespace TeamChallengeApps\Distance\Tests\Feature;

use Orchestra\Testbench\Concerns\WithWorkbench;
use TeamChallengeApps\Distance\Config;
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Tests\TestCase;
use TeamChallengeApps\Distance\Unit;

class DistanceTest extends TestCase {

    use WithWorkbench;

    /** @test */
    public function it_creates_distance_with_default_base_unit_as_centimeters()
    {
        $distance = new DistanceValue(100);
        $this->assertEquals(Unit::Centimeters, $distance->getUnit());
    }

    /** @test */
    public function it_creates_distance_with_custom_base_unit()
    {
        app(Config::class)->setBaseUnit(Unit::Meters);
        $distance = new DistanceValue(100);
        $this->assertEquals(Unit::Meters, $distance->getUnit());
    }

    /** @test */
    public function it_skips_converting_when_equals_set()
    {
        $distance = new DistanceValue(5, Unit::Meters, [
            Unit::Footsteps->value => ( $value = 8 ),
        ]);

        $steps = $distance->convertTo(Unit::Footsteps);
        $this->assertEquals(Unit::Footsteps, $steps->getUnit());
        $this->assertEquals($value, $steps->getValue());
    }

    /** @test */
    public function it_forces_converting_even_when_equals_set()
    {
        $distance = new DistanceValue(5, Unit::Meters, [
            Unit::Footsteps->value => ( $value = 8 ),
        ]);

        $steps = $distance->convertTo(Unit::Footsteps, checkEquals: false);
        $this->assertEquals(Unit::Footsteps, $steps->getUnit());
        $this->assertNotEquals($value, $steps->getValue());
    }

    /** @test */
    public function it_converts_to_meters()
    {
        $units = [
            'centimeters' => [
                [1, 0.01],
                [5, 0.05],
                [100, 1],
                [123, 1.23],
                [1000, 10],
            ],
            'kilometers' => [
                [0.1, 100],
                [1, 1000],
                [5, 5000],
                [100, 100000],
                [123, 123000],
            ],
            'miles' => [
                [0.1, 160.935],
                [1, 1609.347],
                [5, 8046.735],
                [100, 160934.709],
                [123, 197949.692],
            ],
        ];

        foreach ( $units as $unit => $conversions ) {
            foreach ( $conversions as $conversion ) {
                list($value, $result) = $conversion;
                $distance = new DistanceValue($value, $unit);
                $meters = $distance->convertTo(Unit::Meters);
                $this->assertEquals(Unit::Meters, $meters->getUnit());
                $this->assertEquals($result, round($meters->getValue(), 3), $value.' '.$unit.' as meters (expected '.$result.')');
            }
        }
    }

    /** @test */
    public function it_formats_to_decimal()
    {
        $distance = new DistanceValue(100, Unit::Meters);
        $this->assertSame(100.0, $distance->formatDecimal(convert: false));

        $distance = new DistanceValue(100.56678, Unit::Meters);
        $this->assertSame(100.57, $distance->formatDecimal(convert: false));

        $distance = new DistanceValue(100.56678, Unit::Meters);
        $this->assertSame(100.567, $distance->formatDecimal(convert: false, options: ['precision' => 3]));
    }

    /** @test */
    public function it_formats_to_string()
    {
        $distance = new DistanceValue(100, Unit::Meters);
        $this->assertSame('100 meters', $distance->format(convert: false));

        $distance = new DistanceValue(100.56678, Unit::Meters);
        $this->assertSame('100.57 meters', $distance->format(convert: false));
    }

    /** @test */
    public function it_converts_to_default_display_unit_when_formatting()
    {
        app(Config::class)->setDisplayUnit(Unit::Footsteps);
        $distance = new DistanceValue(7, Unit::Meters);

        $this->assertSame('11 steps', $distance->format(convert: true));

    }

}

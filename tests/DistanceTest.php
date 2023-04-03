<?php

use Illuminate\Support\Collection;
use TeamChallengeApps\Distance\Distance;
use PHPUnit\Framework\TestCase;

class DistanceTest extends TestCase
{
    /** @test */
    public function it_creates_distance()
    {
        $distance = new Distance(1);
        $distance = $this->loadConfig($distance);

        $this->assertEquals($distance->value, 1);
        $this->assertEquals($distance->unit, 'meters');
    }

    /** @test **/
    public function it_converts_to_kilometers()
    {
        $map = new Collection([
            '1000' => 1,
            '1500' => 1.5,
            '2750' => 2.75,
        ]);

        foreach ($map as $meters => $km) {
            $distance = new Distance($meters);
            $distance = $this->loadConfig($distance);

            $distance = $distance->toKilometers();

            $this->assertEquals($distance->value, $km);
        }
    }

    /** @test **/
    public function it_converts_from_kilometers()
    {
        $map = new Collection([
            '1' => 1000,
            '1.5' => 1500,
            '2.75' => 2750,
        ]);

        foreach ($map as $km => $meters) {
            $distance = new Distance((float) $km, 'kilometers');
            $distance = $this->loadConfig($distance);

            $distance = $distance->toMeters();

            $this->assertEquals($distance->value, $meters);
        }
    }

    /** @test **/
    public function it_converts_to_miles()
    {
        $map = new Collection([
            '1000' => 0.62,
            '1500' => 0.93,
            '2750' => 1.71,
        ]);

        foreach ($map as $meters => $miles) {
            $distance = new Distance($meters);
            $distance = $this->loadConfig($distance);

            $distance = $distance->toMiles();
            $distance = $this->loadConfig($distance);

            $this->assertEquals($distance->round(), $miles);
        }
    }

    /** @test **/
    public function it_converts_to_steps()
    {
        $map = new Collection([
            '1' => 1,
            '1000' => 1458,
            '1500' => 2187,
            '2750' => 4010,
        ]);

        foreach ($map as $meters => $steps) {
            $distance = new Distance($meters);
            $distance = $this->loadConfig($distance);

            $distance = $distance->toSteps();
            $distance = $this->loadConfig($distance);

            $this->assertEquals($distance->round(), $steps);
        }
    }

    /** @test **/
    public function it_formats_to_string_automatically()
    {
        $meters = 10000;

        $distance = new Distance(10000);
        $distance = $this->loadConfig($distance);

        $string = number_format($meters, 2, '.', ',');

        $this->assertEquals((string) $distance, $string);
    }

    /** @test **/
    public function it_formats_to_steps_string_without_decimals()
    {
        $meters = 10000;

        $distance = new Distance(10000, 'footsteps');
        $distance = $this->loadConfig($distance);

        $string = number_format($meters, 0, '.', ',');

        $this->assertEquals((string) $distance, $string);
    }

    /** @test **/
    public function it_formats_to_steps_string_with_suffix()
    {
        $meters = 10000;

        $distance = new Distance(10000, 'footsteps');
        $distance = $this->loadConfig($distance);

        $string = number_format($meters, 0, '.', ',') . ' steps';

        $this->assertEquals($distance->toStringWithSuffix(), $string);
    }

    /** @test **/
    public function it_allows_global_distance_function()
    {
        $distance = new Distance(1000);
        $helper = distance_value(1000);

        $this->assertEquals($distance, $helper);
    }

    /** @test **/
    public function it_converts_to_unit_value()
    {
        $distance = new Distance(1000);

        $map = new Collection([
            '1000' => 1,
            '1500' => 1.5,
            '2750' => 2.75,
        ]);

        foreach ($map as $meters => $km) {
            $distance = new Distance($meters);
            $distance = $this->loadConfig($distance);

            $this->assertEquals($distance->asUnit('kilometers'), $km);
        }
    }

    /** @test **/
    public function it_calculates_percentages()
    {
        $distance = $this->loadConfig(new Distance(250));
        $total = $this->loadConfig(new Distance(1000));

        $percentage = $distance->percentageOf($total);

        $this->assertEquals($percentage, 25);
    }

    /** @test **/
    public function it_overflows_percentages_by_default()
    {
        $distance = $this->loadConfig(new Distance(1500));
        $total = $this->loadConfig(new Distance(1000));

        $percentage = $distance->percentageOf($total);

        $this->assertEquals($percentage, 150);
    }

    /** @test **/
    public function it_caps_percentage_at_100()
    {
        $distance = $this->loadConfig(new Distance(1500));
        $total = $this->loadConfig(new Distance(1000));

        $percentage = $distance->percentageOf($total, false);

        $this->assertEquals($percentage, 100);
    }

    /** @test **/
    public function it_decrements_distance()
    {
        $distance = $this->loadConfig(new Distance(1500));
        $subtract = $this->loadConfig(new Distance(500));

        $distance->decrement($subtract);

        $this->assertEquals($distance->value, 1000);
    }

    /** @test **/
    public function it_stays_clean_after_copying()
    {
        $distance = $this->loadConfig(new Distance(1500));
        $subtract = $this->loadConfig(new Distance(500));

        $after = $distance->copy()->decrement($subtract);

        $this->assertEquals($distance->value, 1500);
        $this->assertEquals($after->value, 1000);
    }

    protected function loadConfig(Distance $distance)
    {
        $config = include __DIR__.'/../src/config/config.php';

        return $distance->setConfig($config);
    }
}

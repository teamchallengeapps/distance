<?php

use TeamChallengeApps\Distance\Distance;

class DistanceTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_creates_distance()
    {
        $distance = new Distance(1);
        $distance = $this->loadConfig($distance);

        $this->assertEquals($distance->value, 1);
        $this->assertEquals($distance->unit, 'meters');
    }

    protected function loadConfig(Distance $distance)
    {
    	$config = include __DIR__.'/../src/config/config.php';

    	return $distance->setConfig($config);
    }

}
<?php

namespace TeamChallengeApps\Distance;

use Illuminate\Support\ServiceProvider;

class DistanceServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->setupConfig();
    }

    /**
     * Setup the package config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $config = realpath(__DIR__.'/config/config.php');

        $this->mergeConfigFrom($config, 'distance');

        $this->publishes([
            $config => config_path('distance.php'),
        ], 'config');
    }

}
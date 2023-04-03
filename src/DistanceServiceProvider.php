<?php

namespace TeamChallengeApps\Distance;

use Illuminate\Support\ServiceProvider;

class DistanceServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->setupConfig();
    }

    /**
     * Setup the package config.
     */
    protected function setupConfig()
    {
        $config = realpath(__DIR__.'/config/config.php');

        $this->mergeConfigFrom($config, 'distance');

        $this->publishes([
            $config => app()->configPath('distance.php'),
        ], 'config');
    }
}

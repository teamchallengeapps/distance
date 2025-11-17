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

        $this->app->singleton(Config::class, function ($app) {
            return new Config(
                baseUnit: config('distance.base_unit'),
                displayUnit: config('distance.display_unit'),
                conversions: config('distance.conversions'),
            );
        });
        $this->app->alias(Config::class, 'distance.config');

        $this->app->singleton(Converter::class, function($app) {
            return new Converter($app['distance.config']);
        });
        $this->app->alias(Converter::class, 'distance.converter');

        $this->app->singleton(DistanceFormatter::class, function($app) {
            return new DistanceFormatter($app);
        });
        $this->app->alias(DistanceFormatter::class, 'distance.formatter');
    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'distance');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/distance'),
        ]);
    }

    /**
     * Setup the package config.
     */
    protected function setupConfig()
    {
        $config = realpath(__DIR__.'/config/distance.php');

        $this->mergeConfigFrom($config, 'distance');

        $this->publishes([
            $config => app()->configPath('distance.php'),
        ], 'config');
    }
}

<?php

namespace TeamChallengeApps\Distance;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use NumberFormatter;
use TeamChallengeApps\Distance\Formatter\AbbreviatedDistanceFormatter;
use TeamChallengeApps\Distance\Formatter\DecimalDistanceFormatter;
use TeamChallengeApps\Distance\Formatter\IntlDistanceFormatter;

class DistanceFormatter extends Manager {

    public function format(DistanceValue $distance, bool $convert = true, array $options = [])
    {
        return $this->formatUsing($this->getDefaultDriver(), $distance, $convert, $options);
    }

    public function formatUsing(string $formatter, DistanceValue $distance, bool $convert = true, array $options = [])
    {
        $displayUnit = app(Config::class)->getDisplayUnit();
        if ( $convert && ! $distance->isUnit($displayUnit) ) {
            $distance = $distance->convertTo($displayUnit);
        }

        return $this->driver($formatter)->format($distance, $options);
    }

    public function createIntlDriver()
    {
        return new IntlDistanceFormatter(new NumberFormatter($this->container->getLocale(), NumberFormatter::DECIMAL));
    }

    public function createAbbreviatedDriver()
    {
        return new AbbreviatedDistanceFormatter(new NumberFormatter($this->container->getLocale(), NumberFormatter::DECIMAL));
    }

    public function createDecimalDriver()
    {
        return new DecimalDistanceFormatter();
    }

    public function getDefaultDriver()
    {
        return 'intl';
    }

}

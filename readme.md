# Distance Helper [![Build Status](https://travis-ci.org/teamchallengeapps/distance.svg?branch=master)](https://travis-ci.org/teamchallengeapps/distance)

## About

This Distance Helper package contains a tested PHP Value Object which makes working with, comparing, converting and formatting distances (inches, centimeters, meters, kilometers, miles and steps) easy and fluent.

The inspiration for the package came from PHP helpers like [Carbon](http://carbon.nesbot.com/), and an effort to refactor the code behind the virtual workplace walking challenge system [Big Team Challenge](https://bigteamchallenge.com).

## Installation

You can pull in this package through composer 

```
composer require teamchallengeapps/distance
```

The package (particularly configuration) is designed to work with Laravel 10+ using autodiscovery. You can manually add our service provider within the providers array:

```
\TeamChallengeApps\Distance\DistanceServiceProvider::class,
```

## Usage

To create a new distance you, simply new-up an instance of the Distance class.

```php

use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Unit;

$meters = new DistanceValue(100, Unit::Meters);
$km = new DistanceValue(10.5, Unit::Kilometers);
$miles = new DistanceValue(10, Unit::Miles);
$steps = new DistanceValue(10000, Unit::Footsteps);

```

The default (base) unit is **centimeters**, so ommitting the second (optional) constructor argument will default to meters. This is chosen to be the smallest (integer) measurement in your datastore - similar to storing integer cents rather than decimal dollars for money.

```php

$centimeters = new DistanceValue(100);

```

## API

### Converting

You can convert a distance object to a new unit using the `convertTo` methods.

```php
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Unit;

$centimeters = new DistanceValue(1000, Unit::Centimeters);
$meters = $meters->toMeters();

echo $meters->getValue(); // 1
```

If you want to convert to the base unit (`centimeters` by default), you can do:

```php
$distance->convertToBase();
```

The conversions are stored inside the `distance.conversions` config in the format, e.g:

```
'conversions' => [
    'centimeters:meters' => 0.01,
    ...
]
```

### Comparison

**Empty / zero**

```php
use TeamChallengeApps\Distance\DistanceValue;

$distance new DistanceValue(0);
// or $distance = DistanceValue::zero();

if ($distance->isZero()) {
  
}

```

**Positive / negative**

```php
use TeamChallengeApps\Distance\DistanceValue;

$distance new DistanceValue(100);

$distance->isPositive(); // true
$distance->isNegative(); // false

```


**Value Comparison**

You can compare two distances, but you will get a `ComparisonException` exception if the units do not match

```php
use TeamChallengeApps\Distance\DistanceValue;

$distance = new DistanceValue(10);
$total = new DistanceValue(100);

if ($distance->equals($total) ) {
  // Equal to
}

if ($distance->lessThan($total) || $distance->lt($total)) {
  // Less than || alias
}

if ($distance->lessThanOrEqual($total) || $distance->lte($total)) {
  // Less than or equal || alias
}

if ($distance->greaterThan($total) || $distance->gt($total)) {
  // Greater than || alias
}

if ($distance->greaterThanOrEqual($total) || $distance->gte($total)) {
  // Greater than or equal || alias
}
```

### Calculations

**Percentage**

```php
use TeamChallengeApps\Distance\DistanceValue;

$distance = new DistanceValue(10);
$total = new DistanceValue(100);

$percentage = $distance->percentageOf($total); // 10

```

By default, the real percentage returned, but passing `false` as the second parameter will cap at 100.

```php
use TeamChallengeApps\Distance\DistanceValue;

$distance = new DistanceValue(150);
$total = new DistanceValue(100);

$percentage = $distance->percentageOf($total); // 150
$percentage = $distance->percentageOf(distance: $total, overflow: false); // 100

```

**Add**

```php
use TeamChallengeApps\Distance\DistanceValue;

$total = new DistanceValue(1000);
$logged = new DistanceValue(10);

$result = $total->add($logged); 

echo $result->getValue(); // 1010

```

**Subtract**

```php

$total = new DistanceValue(1010);
$redeemed = new DistanceValue(10);

$result = $total->subtract($logged); 

echo $result->getValue(); // 1000

```

**Multiply**

```php
use TeamChallengeApps\Distance\DistanceValue;

$value = new DistanceValue(5);

$result = $total->multiply(3); 

echo $result->getValue(); // 15

```

**Divide**

```php
use TeamChallengeApps\Distance\DistanceValue;

$value = new DistanceValue(15);

$result = $total->divide(3); 

echo $result->getValue(); // 5

```

### Formatting

#### String

Using PHP's magic `__toString()` method, echo-ing or casting the object itself will round and use php-intl's NumberFormatter to render as a string.

```php
use TeamChallengeApps\Distance\DistanceValue;

$distance = new DistanceValue(100500.591);

echo $distance; // 100,500.59 centimeters

$value = (string) $distance;

echo $value; // "100,500.59 centimeters"

```

You can change the default precision (2) and rounding mode for each unit in the config file:   

```
php artisan vendor:publish --provider="TeamChallengeApps\Distance\DistanceServiceProvider" --tag="config"
```

```php

return [

    'formatting' => [
        'precision' => [
            'footsteps' => 0,
            'inches' => 0,
        ],
        'translation' => [
            /* Set if you wish to use Laravel pluralization of unit strings */
            'choice' => true,
        ],
        'round' => [
            'footsteps' => \TeamChallengeApps\Distance\RoundingMode::CEILING,
        ],
    ]

];

```

You can also pass options each time you use format:

```php
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Unit;

$meters = new DistanceValue(1.00005, Unit::Meters);

/** Default - auto converted to centimeters (default display) and rounded to 2 decimal places */
echo $meters->format(); // 100.01 centimeters

/** Not converted from original unit but still rounded and using singular suffix */
echo $meters->format(convert: false); // 1 meter

/** Not converted from original unit but still rounded and using singular translated suffix */
echo $meters->format(convert: false, options: ['precision' => 4, 'unit' => false]); // 1.0001
```

#### Abbreviated

There is also an abbreviated string helper for steps:

```php
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Unit;

$steps = new DistanceValue(124000, Unit::Footsteps);
echo $steps->formatUsing(formatter: "abbreviated", convert: false); // 124k steps
```

#### Decimal

```php
use TeamChallengeApps\Distance\DistanceValue;
use TeamChallengeApps\Distance\Unit;

$distance = new DistanceValue(100, Unit::Meters);
echo $distance->formatDecimal(convert: false)); // 100.0

$distance = new DistanceValue(100.56678, Unit::Meters);
echo $distance->formatDecimal(convert: false)); // 100.57

$distance = new DistanceValue(100.56678, Unit::Meters);
echo $distance->formatDecimal(convert: false, options: ['precision' => 3])); // 100.567
```

## Contributing

Please submit improvements and fixes :)

## Changelog

Look at the **[CHANGELOG.md](https://github.com/teamchallengeapps/distance/blob/master/CHANGELOG.md)** for this package.

## Author

[David Rushton](https://github.com/davidrushton) - [Team Challenge Apps Ltd](https://bigteamchallenge.com)

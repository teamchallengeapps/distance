# Distance Helper [![Build Status](https://travis-ci.org/teamchallengeapps/distance.svg?branch=master)](https://travis-ci.org/teamchallengeapps/distance)

## About

This Distance Helper package contains a tested PHP Value Object which makes working with, comparing, converting and formatting distances (meters, kilometers and steps) easy and fluent.

The inspriation for the package came from PHP helpers like [Carbon](http://carbon.nesbot.com/), and an effort to refactor the code behind the virtual workplace walking challenge system [Big Team Challenge](https://bigteamchallenge.com).

## Installation

You can pull in this package through composer 

```
composer require teamchallengeapps/distance
```

The package (particularly configuration) is designed to work with Laravel 5. Include our custom service provider within `config/app.php`:

```php
'providers' => [
    'TeamChallengeApps\Distance\DistanceServiceProvider'
];
```

## Usage

To create a new distance you, simply new-up an instance of the Distance class.

```php

use TeamChallengeApps\Distance\Distance;

$meters = new Distance(100, 'meters');
$km = new Distance(10.5, 'kilometers');
$miles = new Distance(10, 'miles');
$steps = new Distance(10000, 'footsteps');

```

The default distance is **meters**, so ommitting the second (optional) constructor argument will default to meters

```php

$meters = new Distance(100);

```

## API

### Converting

You can convert a distance object to a new unit using the `to` methods.

```php

$meters = new Distance(1000);

$kilometers = $kilometers->toKilometers();

echo $meters->value; // 1

```

The following methods are built-in:

 - `toMeters()`
 - `toKilometers()`
 - `toMiles()`
 - `toFootsteps()`
 - `toSteps()` (alias)

If you just want to get the conversion, without changing the object, you can use the `asUnit` method.

```php

$meters = new Distance(1000);

echo $meters->asUnit('kilometers'); // 1
echo $meters->value; // 1000

```

### Rounding

Each unit has it's own decimal precision, and you can get the rounded format by using the `round` method.

```

$meters = new Distance(1000.995);

echo $meters->value; // 1000.995
echo $meters->round(); // 1001.00

```

### Comparison

**Empty / zero**

```php

$distance new Distance(0);

if ($distance->isEmpty()) {
  //
}

if ($distance->isZero()) {
  
}

```

**Value Comparison**

``php

$distance = new Distance(10);
$total = new Distance(100);

if ($distance->lt($total)) {
  // Less than
}

if ($distance->lte($total)) {
  // Less than or equal
}

if ($distance->gt($total)) {
  // Greater than
}

if ($distance->gte($total)) {
  // Greater than or equal
}

```

**Percentage Of**

``php

$distance = new Distance(10);
$total = new Distance(100);

$percentage = $distance->percentageOf($total); // 10

```

By default, the percentage is capped at 100, but passing `false` as the second parameter will always return the real percentage.

```php

$distance = new Distance(150);
$total = new Distance(100);

$percentage = $distance->percentageOf($total); // 100
$percentage = $distance->percentageOf($total, false); // 150

```

### Modifying 

You can add or subtract distances

```php

$total = new Distance(1000);
$logged = new Distance(10);

$total->increment($logged); 

echo $total->value; // 1010

```

```php

$total = new Distance(1010);
$redeemed = new Distance(10);

$total->decrement($logged); 

echo $total->value; // 1000

```

### Formatting

Using PHP's magic `__toString()` method, echo-ing or casting the object itself will round and use the `number_format` function to return a string-representation of the value.

```php

$distance = new Distance(100500.591);

echo $distance; // 10,500.59

$value = (string) $distance;

echo $value; // 10,500.59

```

## Contributing

Please submit improvements and fixes :)

## Author

[David Rushton](https://github.com/davidrushton) - [Team Challenge Apps Ltd](https://bigteamchallenge.com)
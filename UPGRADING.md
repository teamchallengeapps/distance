# Upgrading

This file details the major breaking changes in an upgrade but is not an exhaustive list.

## From v1 to v2

- Base unit (used for database storage) changed from meters to centimeters. If you used v1 and want to continue using meters, update the config:

```
    'base_unit' => 'meters',
```

- `Distance` has been renamed to `DistanceValue` with the following changes:
    - strongly typed constructor arguments: `float|int $value, Unit|string $unit = null, array $equals = []`
    - `setUnit` and `getUnit` use new `Unit` enum
    - removed config from value object and moved to `Config` singleton class

- Formatting moved to dedicated drivers (using `DistanceFormatter` manager).
    - Implemented: 
        - `AbbreviatedDistanceFormatter` (shorten thousands, millions, billions, trillions) - e.g. 1.2k steps
        - `DecimalDistanceFormatter` for decimal formatting (or int for steps)
        - `IntlDistanceFormatter` default for strings (options for unit suffix).

- Removed `distance_value` and `distance_get` global helper functions.

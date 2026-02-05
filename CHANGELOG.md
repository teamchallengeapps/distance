# Change Log
All notable changes to this package will be documented in this file.

## [Unreleased]

## [2.1.0] - 2026-02-05
- Updated AbbreviatedDistanceFormatter with round and precision options.

## [2.0.0] - 2025-11-17
Major re-factor of package with breaking changes and new flexiblity / functionality.
See [UPGRADING.md](UPGRADING.md)

### Added
- Added Unit enum for strongly typed unit arguments.
- Added new Config singleton class for booting and changing config (e.g. baseUnit).

### Changed
- Renamed Distance class to DistanceValue.
- Updated config with new options.

### Removed
- Removed `distance_value` and `distance_get` global helper functions.

## [1.3.0] - 2023-04-03
### Changed
- Dropped Laravel 5 support.
- Added Laravel 9 and 10 support.

## [1.2.0] - 2021-06-24
### Changed
- Updated to Laravel 6-8 compatibility.

## [1.1.0] - 2017-01-28
### Added
- Added new `toStringWithSuffix` method and updated config with format (toString) options.

## [1.0.1] - 2016-07-27
### Changed
- Updated code formatting to PSR2.

[Unreleased]: https://github.com/teamchallengeapps/distance/compare/2.1.0...HEAD
[2.1.0]: https://github.com/teamchallengeapps/distance/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/teamchallengeapps/distance/compare/1.x...2.0.0
[1.3.0]: https://github.com/teamchallengeapps/distance/compare/1.2.0...1.3.0
[1.3.0]: https://github.com/teamchallengeapps/distance/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/teamchallengeapps/distance/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/teamchallengeapps/distance/compare/1.0.1...1.1.0
[1.0.1]: https://github.com/teamchallengeapps/distance/compare/1.0.0...1.0.1
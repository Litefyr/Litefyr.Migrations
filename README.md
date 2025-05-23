# Migrations for Litefyr

Sometimes, things change. This package helps you to migrate old installation to up-to-date installations.

## Installation

This package is available via [packagist]. Run `composer require litefyr/migrations --no-update` in your
`Litefyr.Distribution` package. After that, run `composer update` in your root directory.

Under the hood, this package uses [Carbon.AutoMigrate]. Read there on how to set up your deployment pipeline.

> In order to work correctly you'll need a working [Litefyr] instance running. Here you'll find the [Distribution] package

[litefyr]: https://litefyr.io
[distribution]: https://github.com/Litefyr/Distribution
[packagist]: https://packagist.org/packages/litefyr/migrations
[carbon.automigrate]: https://github.com/CarbonPackages/Carbon.AutoMigrate

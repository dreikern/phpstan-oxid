# Doctrine extensions for PHPStan

* [PHPStan](https://phpstan.org/)
* [OXID eShop](https://github.com/OXID-eSales/oxideshop_ce)

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```bash
composer require --dev dreikern/phpstan-oxid
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include extension.neon in your project's PHPStan config:

```neon
includes:
    - vendor/dreikern/phpstan-oxid/extension.neon
```
</details>


## Configuration

If the path to your shops module configuration differs from `var/configuration/shops/1.yaml` , you can configure it in your `phpstan.neon`:

```neon
parameters:
    oxid:
        shopConfigurationPath: var/configuration/shops/1.yaml
```

If you need to analyze different subshops without changing your `phpstan.neon` you are able to set the path via environment variable:

```shell
PHPSTAN_OXID_CONFIG_PATH=path/to/config/1.yaml ./vendor/bin/phpstan analyze
```

## Credits

This PHPStan extensions is heavily inspired by [phpstan-doctrine](https://github.com/phpstan/phpstan-doctrine)

# OXID eShop extensions for PHPStan

[![Build](https://github.com/dreikern/phpstan-oxid/workflows/Build/badge.svg)](https://github.com/dreikern/phpstan-oxid/actions)
[![Latest Stable Version](https://poser.pugx.org/dreikern/phpstan-oxid/v/stable)](https://packagist.org/packages/dreikern/phpstan-oxid)
[![License](https://poser.pugx.org/dreikern/phpstan-oxid/license)](https://packagist.org/packages/dreikern/phpstan-oxid)

* [PHPStan](https://phpstan.org/)
* [OXID eShop](https://github.com/OXID-eSales/oxideshop_ce)

This extension provides following features:

* OXID eShop uses class chaining in order to inject modules into their system. This class chain is built at runtime so PHPStan, as a static
code analyzer, isn't able to detect that. This extension reads your shop configuration (e.g. `var/configuration/shops/1.yaml`) and builds this
class chain when PHPStan is analyzing your code. Only activated modules are considered. This allows PHPStan to do its magic.
* When using `oxNew()` or `Registry::get()` this extension dynamically changes the return type when PHPStan is analyzing your code, so it
is aware of any changes your code adds to OXID eShop classes.
* Stubs [some](stubs) OXID eShops classes to fix incorrect phpdoc comments in OXID eShop. Feel free to contribute more stubs when you encounter
such mistakes. [PHPStan Documentation](https://phpstan.org/user-guide/stub-files)
* Provide [rules](#rules) to detect usage of legacy class names (e.g. `oxdiscount` instead of `\OxidEsales\Eshop\Application\Model\Discount`) or
classes without unified namespace (e.g. `\OxidEsales\EshopCommunity\Application\Model\Voucher` instead of `\OxidEsales\Eshop\Application\Model\Voucher`).

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

If the path to your shops module configuration differs from `var/configuration/shops/1.yaml` , you can override the path in your `phpstan.neon`:

```neon
parameters:
    oxid:
        shopConfigurationPath: var/configuration/shops/1.yaml
```

If you need to analyze different subshops without changing your `phpstan.neon` you are able to set the path via environment variable:

```shell
PHPSTAN_OXID_CONFIG_PATH=path/to/config/1.yaml ./vendor/bin/phpstan analyze path/to/oxid/module
```

## Rules

### OxNewCalledWithEditionNamespaceRule

oxNew() call with edition namespace for class `%s`. Use `%s` instead.

- class: [`dreikern\PhpStanOxid\Rule\OxNewCalledWithEditionNamespaceRule`](src/Rule/OxNewCalledWithEditionNamespaceRule.php)

```php
oxNew(\OxidEsales\EshopCommunity\Application\Model\Voucher::class);
```

:x:

<br>

```php
oxNew(\OxidEsales\Eshop\Application\Model\Voucher::class);
```

:white_check_mark:

### OxNewCalledWithLegacyClassNameRule

oxNew() call with legacy className `%s`. Use `%s` instead.

- class: [`dreikern\PhpStanOxid\Rule\OxNewCalledWithLegacyClassNameRule`](src/Rule/OxNewCalledWithLegacyClassNameRule.php)

```php
oxNew('oxdiscount');
```

:x:

<br>

```php
oxNew(\OxidEsales\Eshop\Application\Model\Discount::class);
```

:white_check_mark:

### RegistryGetCalledWithEditionNamespaceRule

Registry::get() call with edition namespace for class `%s`. Use `%s` instead.

- class: [`dreikern\PhpStanOxid\Rule\RegistryGetCalledWithEditionNamespaceRule`](src/Rule/RegistryGetCalledWithEditionNamespaceRule.php)

```php
Registry::get('oxdiscount');
```

:x:

<br>

```php
Registry::get(\OxidEsales\Eshop\Application\Model\Discount::class);
```

:white_check_mark:

### RegistryGetCalledWithLegacyClassNameRule

Registry::get() call with legacy className `%s`. Use `%s` instead.

- class: [`dreikern\PhpStanOxid\Rule\RegistryGetCalledWithLegacyClassNameRule`](src/Rule/RegistryGetCalledWithLegacyClassNameRule.php)

```php
Registry::get(\OxidEsales\EshopCommunity\Application\Model\Voucher::class);
```

:x:

<br>

```php
Registry::get(\OxidEsales\Eshop\Application\Model\Voucher::class);
```

:white_check_mark:

## Credits

This PHPStan extensions is heavily inspired by [phpstan-doctrine](https://github.com/phpstan/phpstan-doctrine)

<?php declare(strict_types=1);

use OxidEsales\Eshop\Core\ViewConfig;

use function PHPStan\Testing\assertType;

assertType(Acme\OxidProject\EnabledModule\Core\ViewConfig::class, oxNew(ViewConfig::class));

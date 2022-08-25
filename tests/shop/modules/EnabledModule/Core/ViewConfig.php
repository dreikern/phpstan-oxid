<?php declare(strict_types=1);

namespace Acme\OxidProject\EnabledModule\Core;

class ViewConfig extends ViewConfig_parent
{
    public string $test_newProperty = '';

    public function test_newMethod(): void
    {
    }
}

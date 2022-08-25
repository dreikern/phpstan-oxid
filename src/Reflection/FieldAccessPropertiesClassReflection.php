<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\StringType;

class FieldAccessPropertiesClassReflection implements PropertiesClassReflectionExtension
{
    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (!$classReflection->isSubclassOf(\OxidEsales\EshopCommunity\Core\Field::class)) {
            return false;
        }

        return in_array($propertyName, [
            'value',
            'rawValue',
        ]);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        $type = new StringType();

        return new ObjectPropertyReflection($classReflection, $type);
    }
}
